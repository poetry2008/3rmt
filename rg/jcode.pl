package jcode;
;#
;# Call initialize function if it is not called yet.  This may sound
;# strange but it makes easy to embed the jcode.pl at the end of
;# script.  Call &jcode'init at the beginning of the script in that
;# case.
;#
&init unless defined $version;

;#
;# Initialize variables.
;#
sub init {
    $version = $rcsid =~ /,v ([\d.]+)/ ? $1 : 'unknown';

    $re_bin  = '[\000-\006\177\377]';

    $re_jis0208_1978 = '\e\$\@';
    $re_jis0208_1983 = '\e\$B';
    $re_jis0208_1990 = '\e&\@\e\$B';
    $re_jis0208 = "$re_jis0208_1978|$re_jis0208_1983|$re_jis0208_1990";
    $re_jis0212 = '\e\$\(D';
    $re_jp      = "$re_jis0208|$re_jis0212";
    $re_asc     = '\e\([BJ]';
    $re_kana    = '\e\(I';

    $esc_0208 = "\e\$B";
    $esc_0212 = "\e\$(D";
    $esc_asc  = "\e(B";
    $esc_kana = "\e(I";

    $re_sjis_c    = '[\201-\237\340-\374][\100-\176\200-\374]';
    $re_sjis_kana = '[\241-\337]';

    $re_euc_c    = '[\241-\376][\241-\376]';
    $re_euc_kana = '\216[\241-\337]';
    $re_euc_0212 = '\217[\241-\376][\241-\376]';

    # Use `geta' for undefined character code
    $undef_sjis = "\x81\xac";

    $cache = 1;

    # X0201 -> X0208 KANA conversion table.  Looks weird?  Not that
    # much.  This is simply JIS text without escape sequences.
    ($h2z_high = $h2z = <<'__TABLE_END__') =~ tr/\041-\176/\241-\376/;
!	!#	$	!"	%	!&	"	!V	#	!W
^	!+	_	!,	0	!<
'	%!	(	%#	)	%%	*	%'	+	%)
,	%c	-	%e	.	%g	/	%C
1	%"	2	%$	3	%&	4	%(	5	%*
6	%+	7	%-	8	%/	9	%1	:	%3
6^	%,	7^	%.	8^	%0	9^	%2	:^	%4
;	%5	<	%7	=	%9	>	%;	?	%=
;^	%6	<^	%8	=^	%:	>^	%<	?^	%>
@	%?	A	%A	B	%D	C	%F	D	%H
@^	%@	A^	%B	B^	%E	C^	%G	D^	%I
E	%J	F	%K	G	%L	H	%M	I	%N
J	%O	K	%R	L	%U	M	%X	N	%[
J^	%P	K^	%S	L^	%V	M^	%Y	N^	%\
J_	%Q	K_	%T	L_	%W	M_	%Z	N_	%]
O	%^	P	%_	Q	%`	R	%a	S	%b
T	%d			U	%f			V	%h
W	%i	X	%j	Y	%k	Z	%l	[	%m
\	%o	]	%s	&	%r	3^	%t
__TABLE_END__
    %h2z = split(/\s+/, $h2z . $h2z_high);
    %z2h = reverse %h2z;

    $convf{'jis'  , 'jis' } = *jis2jis;
    $convf{'jis'  , 'sjis'} = *jis2sjis;
    $convf{'jis'  , 'euc' } = *jis2euc;
    $convf{'euc'  , 'jis' } = *euc2jis;
    $convf{'euc'  , 'sjis'} = *euc2sjis;
    $convf{'euc'  , 'euc' } = *euc2euc;
    $convf{'sjis' , 'jis' } = *sjis2jis;
    $convf{'sjis' , 'sjis'} = *sjis2sjis;
    $convf{'sjis' , 'euc' } = *sjis2euc;
    $h2zf{'jis' } = *h2z_jis;
    $z2hf{'jis' } = *z2h_jis;
    $h2zf{'euc' } = *h2z_euc;
    $z2hf{'euc' } = *z2h_euc;
    $h2zf{'sjis'} = *h2z_sjis;
    $z2hf{'sjis'} = *z2h_sjis;
}

;#
;# Set escape sequences which should be put before and after Japanese
;# (JIS X0208) string.
;#
sub jis_inout {
    $esc_0208 = shift || $esc_0208;
    $esc_0208 = "\e\$$esc_0208" if length($esc_0208) == 1;
    $esc_asc = shift || $esc_asc;
    $esc_asc = "\e\($esc_asc" if length($esc_asc) == 1;
    ($esc_0208, $esc_asc);
}

;#
;# Get JIS in and out sequences from the string.
;#
sub get_inout {
    local($esc_0208, $esc_asc);
    $_[$[] =~ /($re_jis0208)/o && ($esc_0208 = $1);
    $_[$[] =~ /($re_asc)/o && ($esc_asc = $1);
    ($esc_0208, $esc_asc);
}

;#
;# Recognize character code.
;#
sub getcode {
    local(*s) = @_;
    local($matched, $code);

    if ($s !~ /[\e\200-\377]/) {	# not Japanese
	$matched = 0;
	$code = undef;
    }					# 'jis'
    elsif ($s =~ /$re_jp|$re_asc|$re_kana/o) {
	$matched = 1;
	$code = 'jis';
    }
    elsif ($s =~ /$re_bin/o) {		# 'binary'
	$matched = 0;
	$code = 'binary';
    }
    else {				# should be 'euc' or 'sjis'
	local($sjis, $euc) = (0, 0);

	while ($s =~ /(($re_sjis_c)+)/go) {
	    $sjis += length($1);
	}
	while ($s =~ /(($re_euc_c|$re_euc_kana|$re_euc_0212)+)/go) {
	    $euc  += length($1);
	}
	$matched = &max($sjis, $euc);
	$code = ('euc', undef, 'sjis')[($sjis<=>$euc) + $[ + 1];
    }
    wantarray ? ($matched, $code) : $code;
}
sub max { $_[ $[ + ($_[ $[ ] < $_[ $[ + 1 ]) ]; }

;#
;# Convert any code to specified code.
;#
sub convert {
    local(*s, $ocode, $icode, $opt) = @_;
    return (undef, undef) unless $icode = $icode || &getcode(*s);
    return (undef, $icode) if $icode eq 'binary';
    $ocode = 'jis' unless $ocode;
    $ocode = $icode if $ocode eq 'noconv';
    local(*f) = $convf{$icode, $ocode};
    &f(*s, $opt);
    wantarray ? (*f, $icode) : $icode;
}

;#
;# Easy return-by-value interfaces.
;#
sub jis  { &to('jis',  @_); }
sub euc  { &to('euc',  @_); }
sub sjis { &to('sjis', @_); }
sub to {
    local($ocode, $s, $icode, $opt) = @_;
    &convert(*s, $ocode, $icode, $opt);
    $s;
}
sub what {
    local($s) = @_;
    &getcode(*s);
}
sub trans {
    local($s) = shift;
    &tr(*s, @_);
    $s;
}

;#
;# SJIS to JIS
;#
sub sjis2jis {
    local(*s, $opt, $n) = @_;
    &sjis2sjis(*s, $opt) if $opt;
    $s =~ s/(($re_sjis_c|$re_sjis_kana)+)/&_sjis2jis($1) . $esc_asc/geo;
    $n;
}
sub _sjis2jis {
    local($s) = shift;
    $s =~ s/(($re_sjis_c)+|($re_sjis_kana)+)/&__sjis2jis($1)/geo;
    $s;
}
sub __sjis2jis {
    local($s) = shift;
    if ($s =~ /^$re_sjis_kana/o) {
	$n += $s =~ tr/\241-\337/\041-\137/;
	$esc_kana . $s;
    } else {
	$n += $s =~ s/($re_sjis_c)/$s2e{$1}||&s2e($1)/geo;
	$s =~ tr/\241-\376/\041-\176/;
	$esc_0208 . $s;
    }
}

;#
;# EUC to JIS
;#
sub euc2jis {
    local(*s, $opt, $n) = @_;
    &euc2euc(*s, $opt) if $opt;
    $s =~ s/(($re_euc_c|$re_euc_kana|$re_euc_0212)+)/
	&_euc2jis($1) . $esc_asc
    /geo;
    $n;
}
sub _euc2jis {
    local($s) = shift;
    $s =~ s/(($re_euc_c)+|($re_euc_kana)+|($re_euc_0212)+)/&__euc2jis($1)/geo;
    $s;
}
sub __euc2jis {
    local($s) = shift;
    local($esc);

    if ($s =~ tr/\216//d) {
	$esc = $esc_kana;
    } elsif ($s =~ tr/\217//d) {
	$esc = $esc_0212;
    } else {
	$esc = $esc_0208;
    }

    $n += $s =~ tr/\241-\376/\041-\176/;
    $esc . $s;
}

;#
;# JIS to EUC
;#
sub jis2euc {
    local(*s, $opt, $n) = @_;
    $s =~ s/($re_jp|$re_asc|$re_kana)([^\e]*)/&_jis2euc($1,$2)/geo;
    &euc2euc(*s, $opt) if $opt;
    $n;
}
sub _jis2euc {
    local($esc, $s) = @_;
    if ($esc !~ /^$re_asc/o) {
	$n += $s =~ tr/\041-\176/\241-\376/;
	if ($esc =~ /^$re_kana/o) {
	    $s =~ s/([\241-\337])/\216$1/g;
	}
	elsif ($esc =~ /^$re_jis0212/o) {
	    $s =~ s/([\241-\376][\241-\376])/\217$1/g;
	}
    }
    $s;
}

;#
;# JIS to SJIS
;#
sub jis2sjis {
    local(*s, $opt, $n) = @_;
    &jis2jis(*s, $opt) if $opt;
    $s =~ s/($re_jp|$re_asc|$re_kana)([^\e]*)/&_jis2sjis($1,$2)/geo;
    $n;
}
sub _jis2sjis {
    local($esc, $s) = @_;
    if ($esc =~ /^$re_jis0212/o) {
	$s =~ s/../$undef_sjis/g;
	$n = length;
    }
    elsif ($esc !~ /^$re_asc/o) {
	$n += $s =~ tr/\041-\176/\241-\376/;
	if ($esc =~ /^$re_jp/o) {
	    $s =~ s/($re_euc_c)/$e2s{$1}||&e2s($1)/geo;
	}
    }
    $s;
}

;#
;# SJIS to EUC
;#
sub sjis2euc {
    local(*s, $opt,$n) = @_;
    $n = $s =~ s/($re_sjis_c|$re_sjis_kana)/$s2e{$1}||&s2e($1)/geo;
    &euc2euc(*s, $opt) if $opt;
    $n;
}
sub s2e {
    local($c1, $c2, $code);
    ($c1, $c2) = unpack('CC', $code = shift);

    if (0xa1 <= $c1 && $c1 <= 0xdf) {
	$c2 = $c1;
	$c1 = 0x8e;
    } elsif (0x9f <= $c2) {
	$c1 = $c1 * 2 - ($c1 >= 0xe0 ? 0xe0 : 0x60);
	$c2 += 2;
    } else {
	$c1 = $c1 * 2 - ($c1 >= 0xe0 ? 0xe1 : 0x61);
	$c2 += 0x60 + ($c2 < 0x7f);
    }
    if ($cache) {
	$s2e{$code} = pack('CC', $c1, $c2);
    } else {
	pack('CC', $c1, $c2);
    }
}

;#
;# EUC to SJIS
;#
sub euc2sjis {
    local(*s, $opt,$n) = @_;
    &euc2euc(*s, $opt) if $opt;
    $n = $s =~ s/($re_euc_c|$re_euc_kana|$re_euc_0212)/$e2s{$1}||&e2s($1)/geo;
}
sub e2s {
    local($c1, $c2, $code);
    ($c1, $c2) = unpack('CC', $code = shift);

    if ($c1 == 0x8e) {		# SS2
	return substr($code, 1, 1);
    } elsif ($c1 == 0x8f) {	# SS3
	return $undef_sjis;
    } elsif ($c1 % 2) {
	$c1 = ($c1>>1) + ($c1 < 0xdf ? 0x31 : 0x71);
	$c2 -= 0x60 + ($c2 < 0xe0);
    } else {
	$c1 = ($c1>>1) + ($c1 < 0xdf ? 0x30 : 0x70);
	$c2 -= 2;
    }
    if ($cache) {
	$e2s{$code} = pack('CC', $c1, $c2);
    } else {
	pack('CC', $c1, $c2);
    }
}

;#
;# JIS to JIS, SJIS to SJIS, EUC to EUC
;#
sub jis2jis {
    local(*s, $opt) = @_;
    $s =~ s/$re_jis0208/$esc_0208/go;
    $s =~ s/$re_asc/$esc_asc/go;
    &h2z_jis(*s) if $opt =~ /z/;
    &z2h_jis(*s) if $opt =~ /h/;
}
sub sjis2sjis {
    local(*s, $opt) = @_;
    &h2z_sjis(*s) if $opt =~ /z/;
    &z2h_sjis(*s) if $opt =~ /h/;
}
sub euc2euc {
    local(*s, $opt) = @_;
    &h2z_euc(*s) if $opt =~ /z/;
    &z2h_euc(*s) if $opt =~ /h/;
}

;#
;# Cache control functions
;#
sub cache {
    ($cache, $cache = 1)[$[];
}
sub nocache {
    ($cache, $cache = 0)[$[];
}
sub flushcache {
    undef %e2s;
    undef %s2e;
}

;#
;# X0201 -> X0208 KANA conversion routine
;#
sub h2z_jis {
    local(*s, $n) = @_;
    if ($s =~ s/$re_kana([^\e]*)/$esc_0208 . &_h2z_jis($1)/geo) {
	1 while $s =~ s/(($re_jis0208)[^\e]*)($re_jis0208)/$1/o;
    }
    $n;
}
sub _h2z_jis {
    local($s) = @_;
    $n += $s =~ s/(([\041-\137])([\136\137])?)/
	$h2z{$1} || $h2z{$2} . $h2z{$3}
    /ge;
    $s;
}

sub h2z_euc {
    local(*s) = @_;
    $s =~ s/\216([\241-\337])(\216([\336\337]))?/
	$h2z{"$1$3"} || $h2z{$1} . $h2z{$3}
    /ge;
}

sub h2z_sjis {
    local(*s, $n) = @_;
    $s =~ s/(($re_sjis_c)+)|(([\241-\337])([\336\337])?)/
	$1 || ($n++, $h2z{$3} ? $e2s{$h2z{$3}} || &e2s($h2z{$3})
			      : &e2s($h2z{$4}) . ($5 && &e2s($h2z{$5})))
    /geo;
    $n;
}

;#
;# X0208 -> X0201 KANA conversion routine
;#
sub z2h_jis {
    local(*s, $n) = @_;
    $s =~ s/($re_jis0208)([^\e]+)/&_z2h_jis($2)/geo;
    $n;
}
sub _z2h_jis {
    local($s) = @_;
    $s =~ s/((\%[!-~]|![\#\"&VW+,<])+|([^!%][!-~]|![^\#\"&VW+,<])+)/
	&__z2h_jis($1)
    /ge;
    $s;
}
sub __z2h_jis {
    local($s) = @_;
    return $esc_0208 . $s unless $s =~ /^%/ || $s =~ /^![\#\"&VW+,<]/;
    $n += length($s) / 2;
    $s =~ s/(..)/$z2h{$1}/g;
    $esc_kana . $s;
}

sub z2h_euc {
    local(*s, $n) = @_;
    &init_z2h_euc unless defined %z2h_euc;
    $s =~ s/($re_euc_c|$re_euc_kana)/
	$z2h_euc{$1} ? ($n++, $z2h_euc{$1}) : $1
    /geo;
    $n;
}

sub z2h_sjis {
    local(*s, $n) = @_;
    &init_z2h_sjis unless defined %z2h_sjis;
    $s =~ s/($re_sjis_c)/$z2h_sjis{$1} ? ($n++, $z2h_sjis{$1}) : $1/geo;
    $n;
}

;#
;# Initializing JIS X0208 to X0201 KANA table for EUC and SJIS.  This
;# can be done in &init but it's not worth doing.  Similarly,
;# precalculated table is not worth to occupy the file space and
;# reduce the readability.  The author personnaly discourages to use
;# X0201 Kana character in the any situation.
;#
sub init_z2h_euc {
    local($k, $s);
    while (($k, $s) = each %z2h) {
	$s =~ s/([\241-\337])/\216$1/g && ($z2h_euc{$k} = $s);
    }
}
sub init_z2h_sjis {
    local($s, $v);
    while (($s, $v) = each %z2h) {
	$s =~ /[\200-\377]/ && ($z2h_sjis{&e2s($s)} = $v);
    }
}

;#
;# TR function for 2-byte code
;#
sub tr {
    # $prev_from, $prev_to, %table are persistent variables
    local(*s, $from, $to, $opt) = @_;
    local(@from, @to);
    local($jis, $n) = (0, 0);
    
    $jis++, &jis2euc(*s) if $s =~ /$re_jp|$re_asc|$re_kana/o;
    $jis++ if $to =~ /$re_jp|$re_asc|$re_kana/o;

    if (!defined($prev_from) || $from ne $prev_from || $to ne $prev_to) {
	($prev_from, $prev_to) = ($from, $to);
	undef %table;
	&_maketable;
    }

    $s =~ s/([\200-\377][\000-\377]|[\000-\377])/
	defined($table{$1}) && ++$n ? $table{$1} : $1
    /ge;

    &euc2jis(*s) if $jis;

    $n;
}

sub _maketable {
    local($ascii) = '(\\\\[\\-\\\\]|[\0-\133\135-\177])';

    &jis2euc(*to) if $to =~ /$re_jp|$re_asc|$re_kana/o;
    &jis2euc(*from) if $from =~ /$re_jp|$re_asc|$re_kana/o;

    grep(s/(([\200-\377])[\200-\377]-\2[\200-\377])/&_expnd2($1)/ge,
	 $from, $to);
    grep(s/($ascii-$ascii)/&_expnd1($1)/geo,
	 $from, $to);

    @to   = $to   =~ /[\200-\377][\000-\377]|[\000-\377]/g;
    @from = $from =~ /[\200-\377][\000-\377]|[\000-\377]/g;
    push(@to, ($opt =~ /d/ ? '' : $to[$#to]) x (@from - @to)) if @to < @from;
    @table{@from} = @to;
}

sub _expnd1 {
    local($s) = @_;
    $s =~ s/\\(.)/$1/g;
    local($c1, $c2) = unpack('CxC', $s);
    if ($c1 <= $c2) {
	for ($s = ''; $c1 <= $c2; $c1++) {
	    $s .= pack('C', $c1);
	}
    }
    $s;
}

sub _expnd2 {
    local($s) = @_;
    local($c1, $c2, $c3, $c4) = unpack('CCxCC', $s);
    if ($c1 == $c3 && $c2 <= $c4) {
	for ($s = ''; $c2 <= $c4; $c2++) {
	    $s .= pack('CC', $c1, $c2);
	}
    }
    $s;
}

1;
