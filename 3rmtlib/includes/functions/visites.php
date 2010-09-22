<?php
/*
  $Id$
*/

/**
* get visitor browser 
* 
* @param string $userAgent
*/
function getBrowserInfo($userAgent)
{
  $browser = '';

  $GLOBALS['browsers'] = array(
    'msie'              => 'IE',
    'microsoft internet explorer' => 'IE',
    'internet explorer'     => 'IE',
    'netscape6'         => 'NS',
    'netscape'            => 'NS',
    'galeon'            => 'GA',
    'phoenix'           => 'PX',
    'firefox'           => 'FF',
    'mozilla firebird'        => 'FB',
    'firebird'            => 'FB',
    'seamonkey'         => 'SM',
    'chimera'           => 'CH',
    'camino'            => 'CA',
    'safari'            => 'SF',
    'k-meleon'            => 'KM',
    'mozilla'           => 'MO',
    'opera'             => 'OP',
    'konqueror'           => 'KO',
    'icab'              => 'IC',
    'lynx'              => 'LX',
    'links'             => 'LI',
    'ncsa mosaic'         => 'MC',
    'amaya'           => 'AM',
    'omniweb'           => 'OW',
    'hotjava'           => 'HJ',
    'browsex'           => 'BX',
    'amigavoyager'          => 'AV',
    'amiga-aweb'          => 'AW',
    'ibrowse'           => 'IB',
    'unknown'           => 'unk'
  );

  $info = array(
  'shortName' => 'unk',
  'longName' => '',
  'major_number' => '',
  'minor_number' => '',
  'version' => ''
  );
  
  foreach($GLOBALS['browsers'] as $key => $value) 
  {
    if(!empty($browser)) $browser .= "|";
    $browser .= $key;
  }
  
  $results = array();
  
  // added fix for Mozilla Suite detection
  if ((preg_match_all("/(mozilla)[\/\sa-z;.0-9-(]+rv:([0-9]+)([.0-9a-z]+)\) gecko\/[0-9]{8}$/i", $userAgent, $results)) 
  ||  (preg_match_all("/($browser)[\/\sa-z(]*([0-9]+)([\.0-9a-z]+)?/i", $userAgent, $results))
    )
   {
    $count = count($results[0])-1;
    
    // browser code
    $info['shortName'] = $GLOBALS['browsers'][strtolower($results[1][$count])];
    $info['longName'] = strtolower($results[1][$count]);
    
    // majeur version number (7 in mozilla 1.7
    $info['major_number'] = $results[2][$count];
    
    // is an minor version number ? If not, 0
    $match = array();
    
    preg_match('/([.\0-9]+)?([\.a-z0-9]+)?/i', $results[3][$count], $match);
    
    if(isset($match[1])) 
    {
      // find minor version number (7 in mozilla 1.7, 9 in firefox 0.9.3)
      $info['minor_number'] = substr($match[1], 0, 2);
    } 
    else 
    {
      $info['minor_number'] = '.0';
    }
    
    $info['version'] = $info['major_number'] . $info['minor_number'];
  } 
  return $info; 
}

/**
* get the visitor os
* 
* @param string $userAgent
* @param array $osList
* 
* @return string 
*/
function getOs($userAgent)
{
  $GLOBALS['osNameToId'] = Array(
  'Nintendo Wii'   => 'Nintendo Wii',
  'PlayStation Portable' => 'PSP',
  'PLAYSTATION 3'  => 'PS3',
  'Windows NT 6.0' => 'Windows NT 6.0',
  'Windows Vista'  => 'Windows Vista',
  'Windows NT 5.2' => 'Windows Server 2003',
  'Windows Server 2003' => 'Windows Server 2003',
  'Windows NT 5.1' => 'Windows XP',
  'Windows XP'     => 'Windows XP',
  'Win98'          => 'Windows 98',
  'Windows 98'     => 'Windows 98',
  'Windows NT 5.0' => 'Windows NT 5.0',
  'Windows 2000'   => 'Windows 2000',
  'Windows NT 4.0' => 'Windows NT 4.0',
  'WinNT'          => 'Windows NT',
  'Windows NT'     => 'Windows NT',
  //'Win 9x 4.90'    => 'WME',
  'Win 9x 4.90'    => 'Win 9x 4.90',
  'Windows Me'     => 'Windows Me',
  'Win32'          => 'Win32',
  'Win95'          => 'Win95',    
  'Windows 95'     => 'Windows 95',
  'Mac_PowerPC'    => 'Mac_PowerPC', 
  'Mac PPC'        => 'Mac PPC',
  'PPC'            => 'PPC',
  'Mac PowerPC'    => 'Mac PowerPC',
  'Mac OS'         => 'Mac OS',
  'Linux'          => 'Linux',
  'SunOS'          => 'SunOS', 
  'FreeBSD'        => 'FreeBSD', 
  'AIX'            => 'AIX', 
  'IRIX'           => 'IRIX', 
  'HP-UX'          => 'HP-UX', 
  'OS/2'           => 'OS/2', 
  'NetBSD'         => 'NetBSD',
  'Unknown'        => 'UNK' 
  );

  for (@reset($GLOBALS['osNameToId']), $ok = false; 
    (list($key, $value) = @each($GLOBALS['osNameToId'])) && !$ok;)
  {
    if ($ok = ereg($key, $userAgent))
    {
      return $value;
      return $key;
    }
  }
  return 'UNK';
}


/**
* get visitor country with both information : Hostname and BrowserLang
* 
* @param string $host
* @param string $lang browser lang

  $browserLang  = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
  $hostExt      = getHostnameExt(getHost($ip));
  $country      = getCountry($hostExt, $browserLang);

* @return string 
*/
function getCountry($host, $lang = '')
{
  $host = getHostnameExt($host);
  $lang = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
  
  $GLOBALS['ispCountryList'] = array(
  "uiowa.edu" => "us",
  "rima-tde.net" => "es",
  "fuse.net" => "us",
  "dslextreme.com" => "us",
  "ukrtel.net" => "ua",
  "charter.com" => "us",
  "qwest.net" => "us",
  "bbtec.net" => "jp",
  "fastres.net" => "it",
  "t-dialin.net" => "de",
  "hotchilli.net" => "uk",
  "eth.net" => "in",
  "telus.net" => "ca",
  "gaoland.net" => "fr",
  "telecomplus.net" => "mu",
  "xo.net" => "us",
  "virgin.net" => "uk",
  "cox.net" => "us",
  "ntl.com" => "uk",
  "proxad.net" => "fr",
  "megaquebec.net" => "ca",
  );
  $GLOBALS['countryList'] = array(
  "xx" => array("unk"),
  "ac" => array("afr"),
  "ad" => array("eur"),
  "ae" => array("asi",96,207,105,205,102,205),
  "af" => array("asi",150,161,131,151,143,152,163,152),
  "ag" => array("ams"),
  "ai" => array("ams"),
  "al" => array("eur",380,364,383,368),
  "am" => array("asi",46,116),
  "an" => array("ams"),
  "ao" => array("afr",268,382,245,359,258,360,264,360,272,361,226,312),
  "aq" => array("aut"),
  "ar" => array("ams",300,380,308,570,284,401,293,402,300,401,322,401),
  "as" => array("oce"),
  "at" => array("eur",325,293),
  "au" => array("oce",305,249,228,184,254,186,264,184,422,162,285,322),
  "aw" => array("ams"),
  "az" => array("asi",67,116),
  "ba" => array("eur",355,332,363,337),
  "bb" => array("ams"),
  "bd" => array("asi",263,198),
  "be" => array("eur",244,257),
  "bf" => array("afr",139,199,130,200,118,193,124,185,124,187,139,199,129,200),
  "bg" => array("eur",415,348,415,352,429,351,435,352,447,350,424,360),
  "bh" => array("asi"),
  "bi" => array("afr",347,298,347,303),
  "bj" => array("afr",161,204,157,209),
  "bm" => array("ams"),
  "bn" => array("asi"),
  "bo" => array("ams",290,319,278,304,278,307,284,306,302,305),
  "br" => array("ams",382,275,340,259,340,262,350,260),
  "bs" => array("ams",218,64,222,53,218,63,232,67),
  "bt" => array("asi",269,184),
  "bw" => array("afr",305,452,282,438,282,440,288,440,308,441,320,441),
  "by" => array("eur",428,217,428,214,436,215,444,216,472,215,449,227),
  "bz" => array("ams",155,105),
  "ca" => array("amn",184,193,352,256,375,253,242,87,267,57,285,43,295,37,313, 77,307,67,287,93,307,
  91,357,127,340,137,304,155,303,169,314,173,274,82,341,137,330,81,400,43,294,36,184,175,195,175,201,174,208,175),
  "cc" => array("oce"),
  "cd" => array("afr",313,325,275,283,281,284,286,285,275,297, 281,296, 293,297,295,298,319,297,331,
  275,310,295,309,307,309,313,310,310),
  "cf" => array("afr",299,235,262,226,269,227,274,228,291,227,309,227,263,241,282,241,295,239),
  "cg" => array("afr",248,286,248,277,260,277),
  "ch" => array("eur",276,297,285,302,289,304),
  "ci" => array("afr",104,223,98,216,107,215,89,227,105,228,117,229,117,227),
  "ck" => array("asi"),
  "cl" => array("ams",264,370,298,559,),
  "cm" => array("afr",220,257,212,247,225,245,234,246),
  "cn" => array("asi",317,135,333,115,366,229),
  "co" => array("ams",238,178,229,189,237,190,251,190,259,188),
  "cs" => array("eur"),
  "cr" => array("ams",172,150,177,153,181,153),
  "cu" => array("ams",207,80),
  "cv" => array("afr"),
  "cy" => array("eur",515,436),
  "cz" => array("eur",327,273,332,264,338,265,351,264),
  "de" => array("eur",287,260,269,240,279,240,293,243,299,242,311,240),
  "dj" => array("afr",429,198),
  "dk" => array("eur",276,192,272,182,299,188,296,198,327,195),
  "dm" => array("ams"),
  "do" => array("ams",255,100,281,106,262,101),
  "dz" => array("afr",153,87,150,81,142,81,156,81,167,81),
  "ec" => array("ams",206,221,200,215,207,213), // TODO add FA EL KO NB JA KO-KR CS
  "ee" => array("eur",416,147,419,138),
  "eg" => array("afr",346,110,331,91,343,92,352,91),
  "eh" => array("afr",51,104,58,86,58,92,68,92,77,92),
  "er" => array("afr",418,186),
  "es" => array("eur",167,366,157,381,163,382,168,381,180,379),
  "et" => array("afr",418,233,417,221,423,221,430,220),
  "fi" => array("eur",421,90,419,74,431,73,437,71),
  "fj" => array("oce"),
  "fk" => array("ams",350,544,343,545),
  "fm" => array("oce"),
  "fr" => array("eur",221,306,219,296,236,293,281,358),
  "ga" => array("afr",216,293,217,285,222,284,228,285),
  "gb" => array("eur",165,228,172,231,184,231,204,229,178,208,139,151,134,163,140,90,185,119,139,150),
  "gd" => array("ams"),
  "ge" => array("asi",50,109,43,104),
  "gf" => array("ams",349,183),
  "gg" => array("eur"),
  "gh" => array("afr",132,221,134,231),
  "gi" => array("afr"),
  "gl" => array("amn",464,107,451,91,457,90,471,93,483,92),
  "gm" => array("afr",28,185),
  "gn" => array("afr",77,213,69,202,74,202),
  "gp" => array("ams"),
  "gq" => array("afr",208,269,213,269,212,266,216,267),
  "gr" => array("eur",398,389,409,389,399,380),
  "gs" => array("eur"),
  "gt" => array("ams",144,120),
  "gw" => array("afr",41,194), // TODO he here ?
  "gy" => array("ams",319,180),
  "hk" => array("asi"),
  "hn" => array("ams",169,122,163,124),
  "hr" => array("eur",344,321),
  "ht" => array("ams",247,99),
  "hu" => array("eur",359,310,363,301,374,299,387,299),
  "id" => array("asi",324,318,311,319,368,326,373,327,390,326,401,326,392,332,375, 360,385,358,420,
  367,415,372,437,371,417,335,433,321,455,320,466,340,380,327),
  "ie" => array("eur",140,198,130,210,129,219,141,218),
  "il" => array("asi",7,168),
  "in" => array("asi",213,200,219,215,225,214,279,191),
  "iq" => array("asi",43,151,52,164,57,163),
  "ir" => array("asi",99,146,99,158,122,149),
  "is" => array("eur",51,53,49,43,61,41,67,40),
  "it" => array("eur",310,346,288,325,297,323,332,413,280,383),
  "jm" => array("ams",217,104),
  "jo" => array("asi",22,162,14,168,15,171),
  "jp" => array("asi",483,129,489,120),
  "ke" => array("afr",403,291,393,276,410,278),
  "kg" => array("asi",177,106,183,102),
  "kh" => array("asi",349,261),
  "ki" => array("oce"),
  "km" => array("afr"),
  "kp" => array("asi",423,109,425,101,425,97),
  "kr" => array("asi",431,117,436,123),
  "kw" => array("asi",66,176),
  "ky" => array("ams"),
  "kz" => array("asi",144,49,136,66,145,66,169,66),
  "la" => array("asi",343,236),
  "lb" => array("asi",11,152),
  "li" => array("eur"),
  "lk" => array("asi",228,285),
  "lr" => array("afr",80,240),
  "ls" => array("afr",327,485,330,487),
  "lt" => array("eur",403,181,405,191,420,188),
  "lu" => array("eur",252,265),
  "lv" => array("eur",428,161,413,167,427,168,441,167),
  "ly" => array("afr",282,93,264,84,252,84),
  "ma" => array("afr",28,79,72,80,93,56,102,54),
  "mc" => array("eur"),
  "md" => array("eur",458,299),
  "mg" => array("afr",450,426,468,404,453,404,448,403,442,404),
  "mh" => array("oce"),
  "mk" => array("eur",393,369,402,364),
  "ml" => array("afr",145,158,126,149),
  "mm" => array("asi",299,227),
  "mn" => array("asi",316,75,298,65,310,65,316,65,325,64),
  "mo" => array("asi"),
  "mq" => array("ams"),
  "mr" => array("afr",52,158,53,145,73,145,86,143),
  "mt" => array("eur",331,431),
  "mu" => array("afr"),
  "mv" => array("asi"),
  "mw" => array("afr",379,382,365,374),
  "mx" => array("ams",85,80,74,62,86,63,98,62),
  "my" => array("asi",333,305,384,316,380,311,392,309,395,304,393,311),
  "mz" => array("afr",402,388,396,376,405,377),
  "na" => array("afr",260,454,261,432,269,430,245,432),
  "nc" => array("oce"),
  "ne" => array("afr",148,187,154,188,196,170,205,159,211,158),
  "ng" => array("afr",191,228,189,211,194,210,206,212),
  "ni" => array("ams",173,134),
  "nl" => array("eur",248,236),
  "no" => array("eur",272,108,264,94,280,92,287,95,292,92),
  "np" => array("asi",233,179,228,176),
  "nr" => array("oce"),
  "nz" => array("oce",420,342),
  "om" => array("asi",111,227,115,216,124,217),
  "pa" => array("ams",194,162),
  "pe" => array("ams",223,253,225,278,232,278,241,279),
  "pf" => array("oce"),
  "pg" => array("oce",318,66,348,63,378,67,387,71,400,76,405,85,409,82),
  "ph" => array("asi",419,239,439,265,427,269,431,275,412,273,441,285),
  "pk" => array("asi",178,171,150,181,145,178,170,181,176,182),
  "pl" => array("eur",357,242,351,225,357,228,364,229,371,228,382,226),
  "pm" => array("amn"),
  "pr" => array("ams"),
  "pt" => array("eur",121,392,117,384,123,384),
  "pw" => array("oce"),
  "py" => array("ams",320,338,308,345,312,347,322,347,327,346,338,347),
  "qa" => array("asi",84,198),
  "re" => array("afr"),
  "ro" => array("eur",403,309,410,311,430,312,443,309,426,322),
  "ru" => array("asi",173,9,207,16,228,17),
  "rs" => array("asi",173,9,207,16,228,17),
  "rw" => array("afr",342,295,346,291),
  "sa" => array("asi",39,179,45,198,54,200,60,199,67,198,77,210,68,210,54,211,50,212),
  "sb" => array("oce"),
  "sc" => array("afr"),
  "sd" => array("afr",338,191,337,176,355,177, 349,176),
  "se" => array("eur",318,99,327,125,333,126,339,125,357,159),
  "sg" => array("asi"),
  "si" => array("eur",333,310,339,310),
  "sk" => array("eur",363,286,364,279,376,279,382,278,396,276,396,278),
  "sl" => array("afr",63,225),
  "sm" => array("eur"),
  "sn" => array("afr",48,180,36,173,41,175,31,173),
  "so" => array("afr",437,268,436,258,450,259,458,257),
  "sr" => array("ams",337,194,339,186),
  "sv" => array("ams",152,129),
  "sy" => array("asi",19,153,23,147),
  "sz" => array("afr",351,466),
  "td" => array("afr",263,195,271,170,276,169),
  "tg" => array("afr",148,218),
  "th" => array("asi",315,231,326,244),
  "tj" => array("asi",174,122),
  "tm" => array("asi",127,128),
  "tn" => array("afr",209,45),
  "to" => array("oce"),
  "tp" => array("oce"),
  "tr" => array("eur",514,394,528,393,521,388,440,393,449,371),
  "tt" => array("ams",306,152),
  "tw" => array("asi",414,203),
  "tz" => array("afr",378,343,367,320,381,320,395,318),
  "ua" => array("eur",437,267,466,271,481,268),
  "ug" => array("afr",359,276,352,270,358,270,369,269,374,270),
  "uk" => array("eur",165,228,172,231,184,231,204,229,178,208,139,151,134,163,140,90,185,119,139,150),
  "gb" => array("eur"),
  "us" => array("amn",196,310,188,297,89,104),
  "uy" => array("ams",343,402,355,411),
  "uz" => array("asi",146,115),
  "va" => array("eur"),
  "ve" => array("ams",260,154,266,164,278,164,292,164,299,166),
  "vn" => array("asi",335,215,343,216),
  "vu" => array("oce"),
  "wf" => array("oce"),
  "ye" => array("asi",86,242,57,248,70,248),
  "yu" => array("eur",382,340,393,339,392,343),
  "za" => array("afr",292,490,267,504,282,505,294,504,302,506,327,505),
  "zm" => array("afr",314,393,309,382,323,381,330,380),
  "cd" => array("afr",313,325,275,283,281,284,286,285,275,297, 281,296, 293,297,309,298,319,297,331,275,
  310,295,309,307,309,313,310,310,321), // zaïre = rep dem congo?
  "zw" => array("afr",344,422,335,410,341,411,346,411,360,409),
  );
  if(isset($GLOBALS['ispCountryList'][$host]))
  {
    return $GLOBALS['ispCountryList'][$host];
  }

  // look for an existing domain
  $domain = substr($host, strrpos($host, '.') + 1);
  
  // if domain doesn't exist
  if(strlen($domain) != 2)
  {
    // look for the the last but one extension Ex : "ca" in ".ca.com"
    $host = substr($host, 0, strrpos($host, '.') );
    $domain = substr($host, strrpos($host, '.'));
  }
  
  if(isset($GLOBALS['countryList'][$domain][0]))
  {
    return $domain;
  }
  else
  {
    // try with the browser langage code
    
    // replace cs language (Serbia Montenegro country code) with czech country code
    $lang = str_replace('cs', 'cz', $lang);
    
    // replace sv language (El Salvador country code) with sweden country code
    $lang = str_replace('sv', 'se', $lang); 
    
    // replace fa language (Unknown country code) with Iran country code
    $lang = str_replace('fa', 'ir', $lang);
    
    // replace ja language (Unknown country code) with japan country code
    $lang = str_replace('ja', 'jp', $lang);
    
    // replace ko language (Unknown country code) with corée country code
    $lang = str_replace('ko', 'kr', $lang); 
    
    // replace he language (Unknown country code) with Israel country code
    $lang = str_replace('he', 'il', $lang);                  
           
    // replace da language (Unknown country code) with Danemark country code
    $lang = str_replace('da', 'dk', $lang);     
        
    
        // Ex: "fr"
    if(strlen($lang) == 2)
    {
      if(isset($GLOBALS['countryList'][$lang][0]))
      {
        return $lang;
      }
    }

    // when comma
    $offcomma = strpos($lang, ',');
  
    if($offcomma == 2)
    {
      // in 'fr,en-us', keep first two chars
      $domain = substr($lang, 0, 2);
      if(isset($GLOBALS['countryList'][$domain][0]))
      {
        return $domain;
      }

      // catch the second language Ex: "fr" in "en,fr"
      $domain = substr($lang, 3, 2);
      if(isset($GLOBALS['countryList'][$domain][0]))
      {
        return $domain;
      }
    }

    // detect second code Ex: "be" in "fr-be"
    $off = strpos($lang, '-');
    if($off!==false)
    {
      $domain = substr($lang, $off+1, 2);
      
      if(isset($GLOBALS['countryList'][$domain][0]))
      {
        return $domain;
      }
    }
    
    // catch the second language Ex: "fr" in "en;q=1.0,fr;q=0.9"
    if(preg_match("/^en;q=[01]\.[0-9],(?P<domain>[a-z]{2});/", $lang, $parts))
    {
      $domain = $parts['domain'];

      if(isset($GLOBALS['countryList'][$domain][0]))
      {
        return $domain;
      }
    }

  }
  
  // unfortunately... unknown !
  return "xx";
}

/**
* returns valid phpmv hostname extension (site.co.jp in fvae.VARG.ceaga.site.co.jp)
* from the complete hostname
* 
* @param string $hostname



* @return string
*/
function getHostnameExt($hostname)
{
  $extToExclude = array(
    'com', 'net', 'org', 'co'
  );
  
  $off = strrpos($hostname, '.');
  $ext = substr($hostname, $off);

  if(empty($off) || is_numeric($ext) || strlen($hostname) < 5)
  {
    return 'Ip';
  }
  else
  {
    $e = explode('.', $hostname);
    $s = sizeof($e);
    
    // if extension not correct
    if(isset($e[$s-2]) && in_array($e[$s-2], $extToExclude))
    {
      return $e[$s-3].".".$e[$s-2].".".$e[$s-1];
    }
    else
    {
      return $e[$s-2].".".$e[$s-1];
    }
  }
}

function parseKeyword($referer){
$searchEngines = array(

//" "   => array(" ", " " [, " "]),

// 1
"1.cz"        => array("1.cz", "q", "iso-8859-2"),
"www.1.cz"      => array("1.cz", "q", "iso-8859-2"),

// 1und1
"portal.1und1.de"   => array("1und1", "search"),

// 3271
"nmsearch.3721.com"   => array("3271", "p"),
"seek.3721.com"     => array("3271", "p"),

// A9
"www.a9.com"      => array("A9", ""),
"a9.com"      => array("A9", ""),

// Abacho
"search.abacho.com"             => array("Abacho", "q"),

// about
"search.about.com"    => array("About", "terms"),

//Acoon
"www.acoon.de"      => array("Acoon", "begriff"),

//Acont
"acont.de"      => array("Acont", "query"),

//Alexa
"www.alexa.com"           => array("Alexa", "q"),
"alexa.com"           => array("Alexa", "q"),

//Alice Adsl
"rechercher.aliceadsl.fr" => array("Alice Adsl", "qs"),
"search.alice.it"               => array("Alice (Virgilio)", "qt"),

//Allesklar
"www.allesklar.de"    => array("Allesklar", "words"),

// AllTheWeb 
"www.alltheweb.com"             => array("AllTheWeb", "q"),

// all.by
"all.by"      => array("All.by", "query"),

// Altavista
"listings.altavista.com"        => array("AltaVista", "q"),
"www.altavista.de"    => array("AltaVista", "q"),
"altavista.fr"      => array("AltaVista", "q"),
"de.altavista.com"    => array("AltaVista", "q"),
"fr.altavista.com"    => array("AltaVista", "q"),
"es.altavista.com"    => array("AltaVista", "q"),
"www.altavista.fr"    => array("AltaVista", "q"),
"search.altavista.com"    => array("AltaVista", "q"),
"search.fr.altavista.com" => array("AltaVista", "q"),
"se.altavista.com"    => array("AltaVista", "q"),
"be-nl.altavista.com"     => array("AltaVista", "q"),
"be-fr.altavista.com"     => array("AltaVista", "q"),
"it.altavista.com"    => array("AltaVista", "q"),
"us.altavista.com"    => array("AltaVista", "q"),
"nl.altavista.com"    => array("Altavista", "q"),
"ch.altavista.com"    => array("AltaVista", "q"),
"www.altavista.com"   => array("AltaVista", "q"),

// APOLLO7
"www.apollo7.de"    => array("Apollo7", "query"),
"apollo7.de"      => array("Apollo7", "query"),

// AOL
"www.aolrecherche.aol.fr" => array("AOL", "q"),
"www.aolrecherches.aol.fr"  => array("AOL", "query"),
"www.aolimages.aol.fr"    => array("AOL", "query"),
"www.recherche.aol.fr"    => array("AOL", "q"),
"aolsearch.aol.com"   => array("AOL", "query"),
"aolsearcht.aol.com"    => array("AOL", "query"),
"find.web.aol.com"    => array("AOL", "query"),
"recherche.aol.ca"    => array("AOL", "query"),
"aolsearch.aol.co.uk"   => array("AOL", "query"),
"search.aol.co.uk"    => array("AOL", "query"),
"aolrecherche.aol.fr"   => array("AOL", "q"),
"sucheaol.aol.de"   => array("AOL", "q"),
"suche.aol.de"      => array("AOL", "q"),

"aolbusqueda.aol.com.mx"  => array("AOL", "query"),
"search.aol.com"    => array("AOL", "query"),

// Aport
"sm.aport.ru"     => array("Aport", "r"),

// Arcor
"www.arcor.de"      => array("Arcor", "Keywords"),

// Arianna (Libero.it)
"arianna.libero.it"     => array("Arianna", "query"),

// Ask
"web.ask.com"     => array("Ask", "ask"),
"www.ask.co.uk"     => array("Ask", "q"),
"uk.ask.com"      => array("Ask", "q"),
"fr.ask.com"      => array("Ask", "q"),
"de.ask.com"      => array("Ask", "q"),
"es.ask.com"      => array("Ask", "q"),
"it.ask.com"      => array("Ask", "q"),
"nl.ask.com"      => array("Ask", "q"),
"ask.jp"      => array("Ask", "q"),
"www.ask.com"     => array("Ask", "ask"),

// Atlas
"search.atlas.cz"     => array("Atlas", "q", "windows-1250"),

// Austronaut
"www2.austronaut.at"    => array("Austronaut", "begriff"),

// Baidu
"www1.baidu.com"    => array("Baidu", "wd"),
"www.baidu.com"     => array("Baidu", "wd"),

// BBC
"search.bbc.co.uk"          => array("BBC", "q"),

// Bellnet
"www.suchmaschine.com"          => array("Bellnet", "suchstr"),

// Biglobe
"cgi.search.biglobe.ne.jp"  => array("Biglobe", "q"),

// Bild
"www.bild.t-online.de"          => array("Bild.de (enhanced by Google)", "query"),

//Blogdigger
"www.blogdigger.com"    => array("Blogdigger","q"),

//Bloglines
"www.bloglines.com"   => array("Bloglines","q"),

//Blogpulse
"www.blogpulse.com"   => array("Blogpulse","query"),

//Bluewin
"search.bluewin.ch"   => array("Bluewin","query"),

// Caloweb
"www.caloweb.de"    => array("Caloweb", "q"),

// Cegetel (Google)
"www.cegetel.net"     => array("Cegetel (Google)", "q"),

// Centrum
"fulltext.centrum.cz"     => array("Centrum", "q", "windows-1250"),
"morfeo.centrum.cz"     => array("Centrum", "q", "windows-1250"),
"search.centrum.cz"     => array("Centrum", "q", "windows-1250"),

// Chello
"www.chello.fr"     => array("Chello", "q1"),

// Club Internet
"recherche.club-internet.fr"    => array("Club Internet", "q"),

// Comcast
"www.comcast.net"     => array("Comcast", "query"),

// Comet systems
"search.cometsystems.com" => array("CometSystems", "q"),

// Compuserve
"suche.compuserve.de"         => array("Compuserve.de (Powered by Google)", "q"),
"websearch.cs.com"              => array("Compuserve.com (Enhanced by Google)", "query"),

// Copernic
"metaresults.copernic.com"  => array("Copernic", " "),

// DasOertliche
"www.dasoertliche.de"         => array("DasOertliche", "kw"),

// DasTelefonbuch
"www.4call.dastelefonbuch.de" => array("DasTelefonbuch", "kw"),

// Defind.de
"suche.defind.de"         => array("Defind.de", "search"),

// Deskfeeds
"www.deskfeeds.com"         => array("Deskfeeds", "sx"),

// Dino
"www.dino-online.de"    => array("Dino", "query"),

// dir.com
"fr.dir.com"      => array("dir.com", "req"),

// dmoz
"editors.dmoz.org"    => array("dmoz", "search"),
"search.dmoz.org"   => array("dmoz", "search"),
"www.dmoz.org"      => array("dmoz", "search"),
"dmoz.org"      => array("dmoz", "search"),

// Dogpile
"search.dogpile.com"    => array("Dogpile", "q"),
"nbci.dogpile.com"    => array("Dogpile", "q"),

// earthlink
"search.earthlink.net"    => array("Earthlink", "q"),

// Eniro
"www.eniro.se"      => array("Eniro", "q"),

// Espotting 
"affiliate.espotting.fr"  => array("Espotting", "keyword"),

// Eudip
"www.eudip.com"     => array("Eudip", " "),

// Eurip
"www.eurip.com"     => array("Eurip", "q"),

// Euroseek
"www.euroseek.com"    => array("Euroseek", "string"),

// Excite
"www.excite.it"     => array("Excite", "q"),
"msxml.excite.com"    => array("Excite", "qkw"),
"www.excite.fr"     => array("Excite", "search"),

// Exalead
"www.exalead.fr"    => array("Exalead", "q"),
"www.exalead.com"   => array("Exalead", "q"),

// eo
"eo.st"       => array("eo", "q"),

// Feedminer
"www.feedminer.com"   => array("Feedminer", "q"),

// Feedster
"www.feedster.com"    => array("Feedster", ""),

// Francite
"antisearch.francite.com" => array("Francite", "KEYWORDS"),
"recherche.francite.com"  => array("Francite", "name"),

// Fireball
"suche.fireball.de"   => array("Fireball", "query"),


// Firstfind
"www.firstsfind.com"    => array("Firstsfind", "qry"),

// Fixsuche
"www.fixsuche.de"   => array("Fixsuche", "q"),

// Flix
"www.flix.de"     => array("Flix.de", "keyword"),

// Free
"search1-2.free.fr"   => array("Free", "q"),
"search1-1.free.fr"   => array("Free", "q"),
"search.free.fr"    => array("Free", "q"),

// Freenet
"suche.freenet.de"    => array("Freenet", "query"),

//Froogle
"froogle.google.de"     => array("Google (Froogle)", "q"),
"froogle.google.com"    => array("Google (Froogle)", "q"),
"froogle.google.co.uk"    => array("Google (Froogle)", "q"),

//GAIS
"gais.cs.ccu.edu.tw"    => array("GAIS)", "query"),

// Gigablast
"www.gigablast.com"     => array("Gigablast", "q"),
"blogs.gigablast.com"     => array("Gigablast (Blogs)", "q"),
"travel.gigablast.com"    => array("Gigablast (Travel)", "q"),
"dir.gigablast.com"     => array("Gigablast (Directory)", "q"),
"gov.gigablast.com"     => array("Gigablast (Gov)", "q"),

// GMX
"suche.gmx.net"     => array("GMX", "su"),
"www.gmx.net"     => array("GMX", "su"),

// goo
"search.goo.ne.jp"    => array("goo", "mt"),
"ocnsearch.goo.ne.jp"   => array("goo", "mt"),


// Powered by Google (add or not?)
"www.charter.net"     => array("Google", "q"),
"brisbane.t-online.de"          => array("Google", "q"),
"www.eniro.se"      => array("Google", "q"),
"www.eniro.no"      => array("Google", "q"),
"miportal.bellsouth.net"        => array("Google", "string"),
"home.bellsouth.net"          => array("Google", "string"),
"pesquisa.clix.pt"    => array("Google", "q"),
"google.startsiden.no"          => array("Google", "q"),
"arianna.libero.it"           => array("Google", "query"),
"google.startpagina.nl"         => array("Google", "q"),
"search.peoplepc.com"           => array("Google", "q"),
"www.google.interia.pl"         => array("Google", "q"),
"buscador.terra.es"           => array("Google", "query"),
"buscador.terra.cl"           => array("Google", "query"),
"buscador.terra.com.br"         => array("Google", "query"),
"www.icq.com"       => array("Google", "q"),
"www.adelphia.net"    => array("Google", "q"),
"www.comcast.net"     => array("Google", "query"),
"so.qq.com"       => array("Google", "word"),
"misc.skynet.be"    => array("Google", "keywords"),
"www.start.no"      => array("Google", "q"),
"verden.abcsok.no"    => array("Google", "q"),
"search.sweetim.com"          => array("Google", "q"),

// Google
"gogole.fr"     => array("Google", "q"),
"www.gogole.fr"     => array("Google", "q"),
"wwwgoogle.fr"      => array("Google", "q"),
"ww.google.fr"      => array("Google", "q"),
"w.google.fr"     => array("Google", "q"),
"www.google.fr"     => array("Google", "q"),
"www.google.fr."    => array("Google", "q"),
"google.fr"     => array("Google", "q"),
"www2.google.com"   => array("Google", "q"),
"w.google.com"      => array("Google", "q"),
"ww.google.com"     => array("Google", "q"),
"wwwgoogle.com"           => array("Google", "q"),
"www.gogole.com"    => array("Google", "q"),
"www.gppgle.com"    => array("Google", "q"),
"go.google.com"     => array("Google", "q"),
"www.google.ae"     => array("Google", "q"),
"www.google.as"     => array("Google", "q"),
"www.google.at"     => array("Google", "q"),
"wwwgoogle.at"      => array("Google", "q"),
"ww.google.at"      => array("Google", "q"),
"w.google.at"     => array("Google", "q"),
"www.google.az"     => array("Google", "q"),
"www.google.be"     => array("Google", "q"),
"www.google.bg"     => array("Google", "q"),
"www.google.ba"     => array("Google", "q"),
"google.bg"     => array("Google", "q"),
"www.google.bi"     => array("Google", "q"),
"www.google.ca"     => array("Google", "q"),
"ww.google.ca"      => array("Google", "q"),
"w.google.ca"     => array("Google", "q"),
"www.google.cc"     => array("Google", "q"),
"www.google.cd"     => array("Google", "q"),
"www.google.cg"     => array("Google", "q"),
"www.google.ch"     => array("Google", "q"),
"ww.google.ch"      => array("Google", "q"),
"w.google.ch"     => array("Google", "q"),
"www.google.ci"     => array("Google", "q"),
"www.google.cl"     => array("Google", "q"),
"www.google.cn"     => array("Google", "q"),
"www.google.co"     => array("Google", "q"),
"www.google.cz"     => array("Google", "q"),
"wwwgoogle.cz"      => array("Google", "q"),
"www.google.de"     => array("Google", "q"),
"ww.google.de"      => array("Google", "q"),
"w.google.de"     => array("Google", "q"),
"wwwgoogle.de"      => array("Google", "q"),
"www.googleearth.de"    => array("Google", "q"),
"googleearth.de"    => array("Google", "q"),
"google.gr"     => array("Google", "q"),
"google.hr"     => array("Google", "q"),
"www.google.dj"     => array("Google", "q"),
"www.google.dk"     => array("Google", "q"),
"www.google.es"     => array("Google", "q"),
"www.google.fi"     => array("Google", "q"),
"www.google.fm"     => array("Google", "q"),
"www.google.gg"     => array("Google", "q"),
"www.googel.fi"     => array("Google", "q"),
"www.googleearth.fr"          => array("Google", "q"),
"www.google.gl"     => array("Google", "q"),
"www.google.gm"     => array("Google", "q"),
"www.google.gr"     => array("Google", "q"),
"www.google.hn"     => array("Google", "q"),
"www.google.hr"     => array("Google", "q"),
"www.google.ie"     => array("Google", "q"),
"www.google.is"     => array("Google", "q"),
"www.google.it"     => array("Google", "q"),
"www.google.jo"     => array("Google", "q"),
"www.google.kz"     => array("Google", "q"),
"www.google.li"     => array("Google", "q"),
"www.google.lt"     => array("Google", "q"),
"www.google.lu"     => array("Google", "q"),
"www.google.lv"     => array("Google", "q"),
"www.google.ms"     => array("Google", "q"),
"www.google.mu"     => array("Google", "q"),
"www.google.mw"     => array("Google", "q"),
"www.google.md"     => array("Google", "q"),
"www.google.nl"     => array("Google", "q"),
"www.google.no"     => array("Google", "q"),
"www.google.pl"     => array("Google", "q"),
"www.google.sk"     => array("Google", "q"),
"www.google.pn"     => array("Google", "q"),
"www.google.pt"     => array("Google", "q"),
"www.google.dk"     => array("Google", "q"),
"www.google.ro"     => array("Google", "q"),
"www.google.ru"     => array("Google", "q"),
"www.google.rw"     => array("Google", "q"),
"www.google.se"     => array("Google", "q"),
"www.google.sn"     => array("Google", "q"),
"www.google.sh"     => array("Google", "q"),
"www.google.si"     => array("Google", "q"),
"www.google.sm"     => array("Google", "q"),
"www.google.td"     => array("Google", "q"),
"www.google.tt"     => array("Google", "q"),
"www.google.uz"     => array("Google", "q"),
"www.google.vg"     => array("Google", "q"),
"www.google.com.ar"   => array("Google", "q"),
"www.google.com.au"   => array("Google", "q"),
"www.google.com.bo"   => array("Google", "q"),
"www.google.com.br"   => array("Google", "q"),
"www.google.com.co"   => array("Google", "q"),
"www.google.com.cu"   => array("Google", "q"),
"www.google.com.ec"   => array("Google", "q"),
"www.google.com.eg"   => array("Google", "q"),
"www.google.com.do"   => array("Google", "q"),
"www.google.com.fj"   => array("Google", "q"),
"www.google.com.gr"           => array("Google", "q"),
"www.google.com.gt"           => array("Google", "q"),
"www.google.com.hk"   => array("Google", "q"),
"www.google.com.ly"   => array("Google", "q"),
"www.google.com.mt"   => array("Google", "q"),
"www.google.com.mx"   => array("Google", "q"),
"www.google.com.my"   => array("Google", "q"),
"www.google.com.nf"   => array("Google", "q"),
"www.google.com.ni"   => array("Google", "q"),
"www.google.com.np"   => array("Google", "q"),
"www.google.com.pa"   => array("Google", "q"),
"www.google.com.pe"           => array("Google", "q"),
"www.google.com.ph"   => array("Google", "q"),
"www.google.com.pk"   => array("Google", "q"),
"www.google.com.pl"   => array("Google", "q"),
"www.google.com.pr"   => array("Google", "q"),
"www.google.com.py"   => array("Google", "q"),
"www.google.com.qa"   => array("Google", "q"),
"www.google.com.om"   => array("Google", "q"),
"www.google.com.ru"   => array("Google", "q"),
"www.google.com.sg"   => array("Google", "q"),
"www.google.com.sa"   => array("Google", "q"),
"www.google.com.sv"   => array("Google", "q"),
"www.google.com.tr"   => array("Google", "q"),
"www.google.com.tw"   => array("Google", "q"),
"www.google.com.ua"   => array("Google", "q"),
"www.google.com.uy"   => array("Google", "q"),
"www.google.com.vc"   => array("Google", "q"),
"www.google.com.vn"   => array("Google", "q"),
"www.google.co.cr"    => array("Google", "q"),
"www.google.co.gg"    => array("Google", "q"),
"www.google.co.hu"    => array("Google", "q"),
"www.google.co.id"    => array("Google", "q"),
"www.google.co.il"    => array("Google", "q"),
"www.google.co.in"    => array("Google", "q"),
"www.google.co.je"    => array("Google", "q"),
"www.google.co.jp"    => array("Google", "q"),
"www.google.co.ls"    => array("Google", "q"),
"www.google.co.ke"    => array("Google", "q"),
"www.google.co.kr"    => array("Google", "q"),
"www.google.co.nz"    => array("Google", "q"),
"www.google.co.th"    => array("Google", "q"),
"www.google.co.uk"    => array("Google", "q"),
"www.google.co.ve"    => array("Google", "q"),
"www.google.co.za"    => array("Google", "q"),
"www.google.co.ma"    => array("Google", "q"),
"www.goggle.com"    => array("Google", "q"),
"www.google.com"    => array("Google", "q"),

//Google Blogsearch
"blogsearch.google.de"    => array("Google Blogsearch", "q"),
"blogsearch.google.fr"    => array("Google Blogsearch", "q"),
"blogsearch.google.co.uk" => array("Google Blogsearch", "q"),
"blogsearch.google.it"    => array("Google Blogsearch", "q"),
"blogsearch.google.net"   => array("Google Blogsearch", "q"),
"blogsearch.google.es"    => array("Google Blogsearch", "q"),
"blogsearch.google.ru"    => array("Google Blogsearch", "q"),
"blogsearch.google.be"    => array("Google Blogsearch", "q"),
"blogsearch.google.nl"    => array("Google Blogsearch", "q"),
"blogsearch.google.at"    => array("Google Blogsearch", "q"),
"blogsearch.google.ch"    => array("Google Blogsearch", "q"),
"blogsearch.google.pl"    => array("Google Blogsearch", "q"),
"blogsearch.google.com"   => array("Google Blogsearch", "q"),


// Google translation
"translate.google.com"    => array("Google Translations", "q"),

// Google Directory
"directory.google.com"    => array("Google Directory", " "),

// Google Images
"images.google.fr"    => array("Google Images", "q"),
"images.google.be"    => array("Google Images", "q"),
"images.google.ca"    => array("Google Images", "q"),
"images.google.co.uk"   => array("Google Images", "q"),
"images.google.de"    => array("Google Images", "q"),
"images.google.be"    => array("Google Images", "q"),
"images.google.ca"    => array("Google Images", "q"),
"images.google.it"        => array("Google Images", "q"),
"images.google.at"    => array("Google Images", "q"),
"images.google.bg"    => array("Google Images", "q"),
"images.google.ch"    => array("Google Images", "q"),
"images.google.ci"    => array("Google Images", "q"),
"images.google.com.au"    => array("Google Images", "q"),
"images.google.com.cu"    => array("Google Images", "q"),
"images.google.co.id"   => array("Google Images", "q"),
"images.google.co.il"   => array("Google Images", "q"),
"images.google.co.in"   => array("Google Images", "q"),
"images.google.co.jp"   => array("Google Images", "q"),
"images.google.co.hu"   => array("Google Images", "q"),
"images.google.co.kr"   => array("Google Images", "q"),
"images.google.co.nz"   => array("Google Images", "q"),
"images.google.co.th"   => array("Google Images", "q"),
"images.google.co.tw"   => array("Google Images", "q"),
"images.google.co.ve"   => array("Google Images", "q"),
"images.google.com.ar"    => array("Google Images", "q"),
"images.google.com.br"    => array("Google Images", "q"),
"images.google.com.cu"    => array("Google Images", "q"),
"images.google.com.do"    => array("Google Images", "q"),
"images.google.com.gr"    => array("Google Images", "q"),
"images.google.com.hk"    => array("Google Images", "q"),
"images.google.com.mx"    => array("Google Images", "q"),
"images.google.com.my"    => array("Google Images", "q"),
"images.google.com.pe"    => array("Google Images", "q"),
"images.google.com.tr"    => array("Google Images", "q"),
"images.google.com.tw"    => array("Google Images", "q"),
"images.google.com.ua"    => array("Google Images", "q"),
"images.google.com.vn"    => array("Google Images", "q"),
"images.google.dk"    => array("Google Images", "q"),
"images.google.es"    => array("Google Images", "q"),
"images.google.fi"    => array("Google Images", "q"),
"images.google.gg"    => array("Google Images", "q"),
"images.google.gr"    => array("Google Images", "q"),
"images.google.it"    => array("Google Images", "q"),
"images.google.ms"    => array("Google Images", "q"),
"images.google.nl"    => array("Google Images", "q"),
"images.google.no"    => array("Google Images", "q"),
"images.google.pl"    => array("Google Images", "q"),
"images.google.pt"    => array("Google Images", "q"),
"images.google.ro"    => array("Google Images", "q"),
"images.google.ru"    => array("Google Images", "q"),
"images.google.se"    => array("Google Images", "q"),
"images.google.sk"    => array("Google Images", "q"),
"images.google.com"   => array("Google Images", "q"),

// Google News
"news.google.se"    => array("Google News", "q"),
"news.google.com"     => array("Google News", "q"),
"news.google.es"    => array("Google News", "q"),
"news.google.ch"    => array("Google News", "q"),
"news.google.lt"    => array("Google News", "q"),
"news.google.ie"    => array("Google News", "q"),
"news.google.de"    => array("Google News", "q"),
"news.google.cl"    => array("Google News", "q"),
"news.google.com.ar"    => array("Google News", "q"),
"news.google.fr"    => array("Google News", "q"),
"news.google.ca"    => array("Google News", "q"),
"news.google.co.uk"     => array("Google News", "q"),
"news.google.co.jp"     => array("Google News", "q"),
"news.google.com.pe"    => array("Google News", "q"),
"news.google.com.au"    => array("Google News", "q"),
"news.google.com.mx"    => array("Google News", "q"),
"news.google.com.hk"    => array("Google News", "q"),
"news.google.co.in"     => array("Google News", "q"),
"news.google.at"    => array("Google News", "q"),
"news.google.com.tw"    => array("Google News", "q"),
"news.google.com.co"    => array("Google News", "q"),
"news.google.co.ve"     => array("Google News", "q"),
"news.google.lu"    => array("Google News", "q"),
"news.google.com.ly"    => array("Google News", "q"),
"news.google.it"    => array("Google News", "q"),
"news.google.sm"    => array("Google News", "q"),
"news.google.com"     => array("Google News", "q"),

// Goyellow.de
"www.goyellow.de"         => array("GoYellow.de", "MDN"),

// HighBeam
"www.highbeam.com"          => array("HighBeam", "Q"),

// Hit-Parade
"recherche.hit-parade.com"  => array("Hit-Parade", "p7"),
"class.hit-parade.com"    => array("Hit-Parade", "p7"),

// Hotbot via Lycos
"hotbot.lycos.com"    => array("Hotbot (Lycos)", "query"),
"search.hotbot.de"    => array("Hotbot", "query"),
"search.hotbot.fr"    => array("Hotbot", "query"),
"www.hotbot.com"    => array("Hotbot", "query"),

// 1stekeuze
"zoek.1stekeuze.nl"     => array("1stekeuze", "terms"),

// Infoseek
"search.www.infoseek.co.jp"     => array("Infoseek", "qt"),

// Icerocket
"blogs.icerocket.com"           => array("Icerocket", "qt"),

// ICQ
"www.icq.com"     => array("ICQ", "q"),

// Ilse
"spsearch.ilse.nl"    => array("Startpagina", "search_for"),
"be.ilse.nl"      => array("Ilse BE", "query"),
"search.ilse.nl"    => array("Ilse NL", "search_for"),

// Iwon
"search.iwon.com"   => array("Iwon", "searchfor"),

// Ixquick
"ixquick.com"     => array("Ixquick", "query"),
"www.eu.ixquick.com"    => array("Ixquick", "query"),
"us.ixquick.com"    => array("Ixquick", "query"),
"s1.us.ixquick.com"   => array("Ixquick", "query"),
"s2.us.ixquick.com"   => array("Ixquick", "query"),
"s3.us.ixquick.com"   => array("Ixquick", "query"),
"s4.us.ixquick.com"   => array("Ixquick", "query"),
"s5.us.ixquick.com"   => array("Ixquick", "query"),
"eu.ixquick.com"    => array("Ixquick","query"),

// Jyxo
"jyxo.cz"       => array("Jyxo", "q"),

// Jungle Spider
"www.jungle-spider.de"    => array("Jungle Spider", "qry"),

// Kartoo
"kartoo.com"      => array("Kartoo", ""),
"kartoo.de"     => array("Kartoo", ""),
"kartoo.fr"     => array("Kartoo", ""),


// Kataweb
"www.kataweb.it"    => array("Kataweb", "q"),

// Klug suchen
"www.klug-suchen.de"            => array("Klug suchen!", "query"),

// La Toile Du Québec via Google
"google.canoe.com"    => array("La Toile Du Québec (Google)", "q"),
"www.toile.com"     => array("La Toile Du Québec (Google)", "q"),  
"web.toile.com"     => array("La Toile Du Québec (Google)", "q"),

// La Toile Du Québec 
"recherche.toile.qc.ca"   => array("La Toile Du Québec", "query"),

// Live.com
"www.live.com"      => array("Live", "q"),
"beta.search.live.com"    => array("Live", "q"),
"search.live.com"   => array("Live", "q"),
"g.msn.com"           => array("Live", " "),

// Looksmart
"www.looksmart.com"   => array("Looksmart", "key"),

// Lycos
"search.lycos.com"    => array("Lycos", "query"),
"vachercher.lycos.fr"   => array("Lycos", "query"),
"www.lycos.fr"      => array("Lycos", "query"),
"suche.lycos.de"    => array("Lycos", "query"),
"search.lycos.de"   => array("Lycos", "query"),
"sidesearch.lycos.com"    => array("Lycos", "query"),
"www.multimania.lycos.fr"   => array("Lycos", "query"),
"buscador.lycos.es"   => array("Lycos", "query"),

// Mail.ru
"go.mail.ru"      => array("Mailru", "q"),

// Mamma
"mamma.com"     => array("Mamma", "query"),
"mamma75.mamma.com"   => array("Mamma", "query"),
"www.mamma.com"     => array("Mamma", "query"),

// Meceoo
"www.meceoo.fr"     => array("Meceoo", "kw"),

// Mediaset
"servizi.mediaset.it"     => array("Mediaset", "searchword"),

// Metacrawler
"search.metacrawler.com"  => array("Metacrawler", "general"),

// Metager
"mserv.rrzn.uni-hannover.de"  => array("Metager", "eingabe"),

// Metager2
"www.metager2.de"         => array("Metager2", "q"),
"metager2.de"                 => array("Metager2", "q"),

// Meinestadt
"www.meinestadt.de"         => array("Meinestadt.de", "words"),

// Monstercrawler
"www.monstercrawler.com"  => array("Monstercrawler", "qry"),

// Mozbot
"www.mozbot.fr"     => array("mozbot", "q"),
"www.mozbot.co.uk"    => array("mozbot", "q"),
"www.mozbot.com"    => array("mozbot", "q"),

// MSN
"beta.search.msn.fr"    => array("MSN", "q"),
"search.msn.fr"     => array("MSN", "q"),
"search.msn.es"     => array("MSN", "q"),
"search.msn.se"     => array("MSN", "q"),
"search.latam.msn.com"    => array("MSN", "q"),
"search.msn.nl"     => array("MSN", "q"),
"leguide.fr.msn.com"    => array("MSN", "s"),
"leguide.msn.fr"    => array("MSN", "s"),
"search.msn.co.jp"    => array("MSN", "q"),
"search.msn.no"     => array("MSN", "q"),
"search.msn.at"     => array("MSN", "q"),
"search.msn.com.hk"   => array("MSN", "q"),
"search.t1msn.com.mx"   => array("MSN", "q"),
"fr.ca.search.msn.com"    => array("MSN", "q"),
"search.msn.be"     => array("MSN", "q"),
"search.fr.msn.be"    => array("MSN", "q"),
"search.msn.it"     => array("MSN", "q"),
"sea.search.msn.it"     => array("MSN", "q"),
"sea.search.msn.fr"     => array("MSN", "q"),
"sea.search.msn.de"     => array("MSN", "q"),
"sea.search.msn.com"    => array("MSN", "q"),
"sea.search.fr.msn.be"    => array("MSN", "q"),
"search.msn.com.tw"     => array("MSN", "q"),
"search.msn.de"     => array("MSN", "q"),
"search.msn.co.uk"    => array("MSN", "q"),
"search.msn.co.za"    => array("MSN", "q"),
"search.msn.ch"     => array("MSN", "q"),
"search.msn.es"     => array("MSN", "q"),
"search.msn.com.br"   => array("MSN", "q"),
"search.ninemsn.com.au"   => array("MSN", "q"),
"search.msn.dk"     => array("MSN", "q"),
"search.arabia.msn.com"   => array("MSN", "q"),
"search.msn.com"    => array("MSN", "q"),
"search.prodigy.msn.com"  => array("MSN", "q"),

// El Mundo
"ariadna.elmundo.es"  => array("El Mundo", "q"),

// MyWebSearch
"kf.mysearch.myway.com"   => array("MyWebSearch", "searchfor"),
"ms114.mysearch.com"    => array("MyWebSearch", "searchfor"),
"ms146.mysearch.com"    => array("MyWebSearch", "searchfor"),
"mysearch.myway.com"    => array("MyWebSearch", "searchfor"),
"searchfr.myway.com"    => array("MyWebSearch", "searchfor"),
"ki.mysearch.myway.com"   => array("MyWebSearch", "searchfor"),
"search.mywebsearch.com"  => array("MyWebSearch", "searchfor"),
"www.mywebsearch.com"   => array("MyWebSearch", "searchfor"),

// Najdi
"www.najdi.si"      => array("Najdi.si", "q"),

// Needtofind
"ko.search.need2find.com" => array("Needtofind", "searchfor"),

// Netster
"www.netster.com"   => array("Netster", "keywords"),

// Netscape
"search-intl.netscape.com"  => array("Netscape", "search"),
"www.netscape.fr"   => array("Netscape", "q"),
"suche.netscape.de"   => array("Netscape", "q"),
"search.netscape.com"   => array("Netscape", "query"),

// Nomade
"ie4.nomade.fr"     => array("Nomade", "s"),
"rechercher.nomade.aliceadsl.fr"=> array("Nomade (AliceADSL)", "s"),
"rechercher.nomade.fr"    => array("Nomade", "s"),

// Northern Light
"www.northernlight.com"   => array("Northern Light", "qr"),

// Numéricable
"www.numericable.fr"    => array("Numéricable", "query"),

// Onet
"szukaj.onet.pl"    => array("Onet.pl", "qt"),

// Opera
"search.opera.com"    => array("Opera", "search"),

// Openfind
"wps.openfind.com.tw"     => array("Openfind (Websearch)", "query"),
"bbs2.openfind.com.tw"    => array("Openfind (BBS)", "query"),
"news.openfind.com.tw"    => array("Openfind (News)", "query"),

// Overture
"www.overture.com"    => array("Overture", "Keywords"),
"www.fr.overture.com"   => array("Overture", "Keywords"),

// Paperball
"suche.paperball.de"    => array("Paperball", "query"),

// Picsearch
"www.picsearch.com"     => array("Picsearch", "q"),

// Plazoo
"www.plazoo.com"    => array("Plazoo", "q"),

// Postami
"www.postami.com"     => array("Postami", "query"),

// Quick searches
"data.quicksearches.net"  => array("QuickSearches", "q"),

// Qualigo
"www.qualigo.de"          => array("Qualigo", "q"),
"www.qualigo.ch"          => array("Qualigo", "q"),
"www.qualigo.at"          => array("Qualigo", "q"),
"www.qualigo.nl"          => array("Qualigo", "q"),

// Rambler
"search.rambler.ru"     => array("Rambler", "words"),

// Reacteur.com
"www.reacteur.com"    => array("Reacteur", "kw"),

// Sapo
"pesquisa.sapo.pt"    => array("Sapo","q"),

// Search.com
"www.search.com"    => array("Search.com", "q"),

// Search.ch
"www.search.ch"     => array("Search.ch", "q"),

// Search a lot
"www.searchalot.com"    => array("Searchalot", "query"),

// Seek
"www.seek.fr"     => array("Searchalot", "qry_str"),

// Seekport
"www.seekport.de"   => array("Seekport", "query"),
"www.seekport.co.uk"    => array("Seekport", "query"),
"www.seekport.fr"   => array("Seekport", "query"),
"www.seekport.at"   => array("Seekport", "query"),
"www.seekport.es"   => array("Seekport", "query"),
"www.seekport.it"   => array("Seekport", "query"),

// Seekport (blogs)
"blogs.seekport.de"   => array("Seekport (Blogs)", "query"),
"blogs.seekport.co.uk"    => array("Seekport (Blogs)", "query"),
"blogs.seekport.fr"   => array("Seekport (Blogs)", "query"),
"blogs.seekport.at"   => array("Seekport (Blogs)", "query"),
"blogs.seekport.es"   => array("Seekport (Blogs)", "query"),
"blogs.seekport.it"   => array("Seekport (Blogs)", "query"),

// Seekport (news)
"news.seekport.de"    => array("Seekport (News)", "query"),
"news.seekport.co.uk"   => array("Seekport (News)", "query"),
"news.seekport.fr"    => array("Seekport (News)", "query"),
"news.seekport.at"    => array("Seekport (News)", "query"),
"news.seekport.es"    => array("Seekport (News)", "query"),
"news.seekport.it"    => array("Seekport (News)", "query"),

// Searchscout
"www.searchscout.com"   => array("Search Scout", "gt_keywords"),

// Searchy
"www.searchy.co.uk"   => array("Searchy", "search_term"),

// Seznam
"search1.seznam.cz"     => array("Seznam", "w"),
"search2.seznam.cz"     => array("Seznam", "w"),
"search.seznam.cz"    => array("Seznam", "w"),

// Sharelook
"www.sharelook.fr"    => array("Sharelook", "keyword"),
"www.sharelook.de"    => array("Sharelook", "keyword"),

// Skynet
"search.skynet.be"    => array("Skynet", "keywords"),

// Sphere
"www.sphere.com"    => array("Sphere", "q"),

// Startpagina
"startgoogle.startpagina.nl"  => array("Startpagina (Google)", "q"),

// Suchnase
"www.suchnase.de"     => array("Suchnase", "qkw"),

// Supereva
"search.supereva.com"     => array("Supereva", "q"),

// Sympatico
"search.sli.sympatico.ca"       => array("Sympatico", "q"),
"search.fr.sympatico.msn.ca"    => array("Sympatico", "q"),
"sea.search.fr.sympatico.msn.ca"=> array("Sympatico", "q"),
"search.sympatico.msn.ca" => array("Sympatico", "q"),

// Suchmaschine.com
"www.suchmaschine.com"    => array("Suchmaschine.com", "suchstr"),

//Technorati
"www.technorati.com"    => array("Technorati", " "),

// Teoma
"www.teoma.com"     => array("Teoma", "t"),

// Tiscali
"rechercher.nomade.tiscali.fr"  => array("Tiscali", "s"),
"search-dyn.tiscali.it"   => array("Tiscali", "key"),
"www.tiscali.co.uk"   => array("Tiscali", "query"),
"search-dyn.tiscali.de"   => array("Tiscali", "key"),
"hledani.tiscali.cz"    => array("Tiscali", "query", "windows-1250"),

// T-Online
"suche.t-online.de"   => array("T-Online", "q"),

// Trouvez.com
"www.trouvez.com"   => array("Trouvez.com", "query"),

// Trusted-Search

"www.trusted--search.com"       => array("Trusted Search", "w"),
 
// Vinden
"zoek.vinden.nl"    => array("Vinden", "query"),

// Vindex
"www.vindex.nl"     => array("Vindex","search_for"),

// Virgilio
"search.virgilio.it"    => array("Virgilio", "qs"),

// Voila
"search.ke.voila.fr"    => array("Voila", "rdata"),
"moteur.voila.fr"   => array("Voila", "kw"),
"search.voila.fr"   => array("Voila", "kw"),
"beta.voila.fr"     => array("Voila", "kw"),
"search.voila.com"    => array("Voila", "kw"),

// Volny
"web.volny.cz"      => array("Volny", "search", "windows-1250"),

// Wanadoo
"search.ke.wanadoo.fr"    => array("Wanadoo", "kw"),
"busca.wanadoo.es"    => array("Wanadoo", "buscar"),

// Web.de
"suche.web.de"      => array("Web.de (Websuche)", "su"),
"dir.web.de"      => array("Web.de (Directory)", "su"),

// Webtip
"www.webtip.de"     => array("Webtip", "keyword"),

// X-recherche
"www.x-recherche.com"     => array("X-Recherche", "mots"),

// Yahoo
"ink.yahoo.com"     => array("Yahoo !", "p"),
"ink.yahoo.fr"      => array("Yahoo !", "p"),
"fr.ink.yahoo.com"    => array("Yahoo !", "p"),
"search.yahoo.co.jp"    => array("Yahoo !", "p"),
"search.yahoo.fr"   => array("Yahoo !", "p"),
"ar.search.yahoo.com"     => array("Yahoo !", "p"),
"br.search.yahoo.com"     => array("Yahoo !", "p"),
"de.search.yahoo.com"   => array("Yahoo !", "p"),
"ca.search.yahoo.com"   => array("Yahoo !", "p"),
"cf.search.yahoo.com"   => array("Yahoo !", "p"),
"fr.search.yahoo.com"   => array("Yahoo !", "p"),
"espanol.search.yahoo.com"  => array("Yahoo !", "p"),
"es.search.yahoo.com"     => array("Yahoo !", "p"),
"id.search.yahoo.com"           => array("Yahoo !", "p"),
"it.search.yahoo.com"     => array("Yahoo !", "p"),
"kr.search.yahoo.com"     => array("Yahoo !", "p"),
"mx.search.yahoo.com"     => array("Yahoo !", "p"),
"nl.search.yahoo.com"     => array("Yahoo !", "p"),
"uk.search.yahoo.com"     => array("Yahoo !", "p"),
"cade.search.yahoo.com"   => array("Yahoo !", "p"),
"tw.search.yahoo.com"     => array("Yahoo !", "p"),
"www.yahoo.com.cn"    => array("Yahoo !", "p"),
"search.yahoo.com"    => array("Yahoo !", "p"),

"de.dir.yahoo.com"              => array("Yahoo ! Webverzeichnis", ""),   
"cf.dir.yahoo.com"    => array("Yahoo ! Répertoires", ""),
"fr.dir.yahoo.com"    => array("Yahoo ! Répertoires", ""),

// Yandex
"www.yandex.ru"     => array("Yandex", "text"),
"yandex.ru"       => array("Yandex", "text"),
"search.yaca.yandex.ru"   => array("Yandex", "text"),
"ya.ru"       => array("Yandex", "text"),
"www.ya.ru"       => array("Yandex", "text"),
"images.yandex.ru"    => array("Yandex Images","text"),

//Yellowmap

"www.yellowmap.de"          => array("Yellowmap", " "),
"yellowmap.de"                  => array("Yellowmap", " "),

// Wanadoo
"search.ke.wanadoo.fr"    => array("Wanadoo", "kw"),
"busca.wanadoo.es"    => array("Wanadoo", "buscar"),

// Wedoo
"fr.wedoo.com"      => array("Wedoo", "keyword"),

// Web.nl
"www.web.nl"      => array("Web.nl","query"),

// Weborama
"www.weborama.fr"   => array("weborama", "query"),

// WebSearch
"is1.websearch.com"   => array("WebSearch", "qkw"),
"www.websearch.com"   => array("WebSearch", "qkw"),
"websearch.cs.com"    => array("WebSearch", "query"),

// Witch
"www.witch.de"            => array("Witch", "search"),

// WXS
"wxsl.nl"       => array("Planet Internet","q"),

// Zoek
"www3.zoek.nl"      => array("Zoek","q"),

// Zhongsou
"p.zhongsou.com"    => array("Zhongsou","w"),

// Zoeken
"www.zoeken.nl"     => array("Zoeken","query"),

// Zoohoo
"zoohoo.cz"       => array("Zoohoo", "q", "windows-1250"),
"www.zoohoo.cz"     => array("Zoohoo", "q", "windows-1250"),

// Zoznam
"www.zoznam.sk"     => array("Zoznam", "s"),
);
  //$searchEngines
  $url_info = parse_url($referer);
  if (isset($searchEngines[$url_info['host']])) {
    if (preg_match_all('/&?'.$searchEngines[$url_info['host']][1].'=([^&]*)&?/', $url_info['query'], $out)) {
      //print_r($out[1][0]);
      return urldecode($out[1][0]);
    }
  }
  return false;
}

function getCountryName ($code) {
$lang = array(
    "xx" => "不明",
    "ac" => "アセンション島",
    "ad" => "アンドラ",
    "ae" => "アラブ首長国連邦",
    "af" => "アフガニスタン",
    "ag" => "アンティグア・バーブーダ",
    "ai" => "アングイラ",
    "al" => "アルバニア",
    "am" => "アルメニア",
    "an" => "オランダ領アンティル",
    "ao" => "アンゴラ",
    "aq" => "南極大陸",
    "ar" => "アルゼンチン",
    "as" => "アメリカ領サモア",
    "at" => "オーストリア",
    "au" => "オーストラリア",
    "aw" => "アルーバ",
    "az" => "アゼルバイジャン",
    "ba" => "ボスニア-ヘルツェゴヴィナ",
    "bb" => "バルバドス",
    "bd" => "バングラデッシュ",
    "be" => "ベルギー",
    "bf" => "ブルキナファソ",
    "bg" => "ブルガリア",
    "bh" => "バーレーン",
    "bi" => "ブルンディ",
    "bj" => "ベニン",
    "bm" => "バーミューダ",
    "bn" => "ブルネイ・ダルサラーム",
    "bo" => "ボリビア",
    "br" => "ブラジル",
    "bs" => "バハマ",
    "bt" => "ブータン",
    "bv" => "ブーヴェ島",
    "bw" => "ボツワナ",
    "by" => "ベラルーシ",
    "bz" => "ベリーズ",
    "ca" => "カナダ",
    "cc" => "ココス(キーリング)島",
    "cd" => "コンゴ民主主義共和国",
    "cf" => "中央アフリカ共和国",
    "cg" => "コンゴ人民共和国",
    "ch" => "スイス",
    "ci" => "コートディボワール",
    "ck" => "クック諸島",
    "cl" => "チリ共和国",
    "cm" => "カメルーン共和国",
    "cn" => "中国",
    "co" => "コロンビア",
    "cr" => "コスタリカ共和国",
  "cs" => "セルビア・モンテネグロ",
    "cu" => "キューバ",
    "cv" => "カーボベルデ",
    "cx" => "クリスマス島",
    "cy" => "キプロス",
    "cz" => "チェコ共和国",
    "de" => "ドイツ",
    "dj" => "ジブチ共和国",
    "dk" => "デンマーク",
    "dm" => "ドミニカ国",
    "do" => "ドミニカ共和国",
    "dz" => "アルジェリア民主人民共和国",
    "ec" => "エクアドル共和国",
    "ee" => "エストニア共和国",
    "eg" => "エジプト",
    "eh" => "西サハラ",
    "er" => "エリトリア",
    "es" => "スペイン",
    "et" => "エチオピア人民民主共和国",
    "fi" => "フィンランド共和国",
    "fj" => "フィジー共和国",
    "fk" => "フォークランド(マルビナス)諸島",
    "fm" => "ミクロネシア連邦",
    "fo" => "フェロー諸島",
    "fr" => "フランス",
    "ga" => "ガボン",
    "gd" => "グレナダ",
    "ge" => "ジョージア",
    "gf" => "フランスガイアナ協同共和国",
    "gg" => "ガーンジー島",
    "gh" => "ガーナ",
    "gi" => "ジブラルタル",
    "gl" => "グリーンランド",
    "gm" => "ガンビア共和国",
    "gn" => "ギニア共和国",
    "gp" => "グアドループ",
    "gq" => "赤道ギニア共和国",
    "gr" => "ギリシア",
    "gs" => "サウスジョージアとサウスサンドイッチ諸島",
    "gt" => "グアテマラ",
    "gu" => "グアム",
    "gw" => "ギニアビサウ共和国",
    "gy" => "ガイアナ協同共和国",
    "hk" => "香港",
    "hm" => "ハード島とマクドナルド島",
    "hn" => "ホンジュラス共和国",
    "hr" => "クロアチア",
    "ht" => "ハイチ",
    "hu" => "ハンガリー",
    "id" => "インドネシア",
    "ie" => "アイルランド",
    "il" => "イスラエル",
    "im" => "マン島",
    "in" => "インディア",
    "io" => "英国のインド洋領域",
    "iq" => "イラク",
    "ir" => "イラン、イスラム共和国",
    "is" => "アイスランド",
    "it" => "イタリア",
    "je" => "ジャージー島",
    "jm" => "ジャマイカ",
    "jo" => "ヨルダン共和国",
    "jp" => "日本",
    "ke" => "ケニア",
    "kg" => "キルギスタン共和国",
    "kh" => "カンボジア国",
    "ki" => "キリバス共和国",
    "km" => "コモロ・イスラム連邦共和国",
    "kn" => "セントキッツとネヴィス",
    "kp" => "北朝鮮",
    "kr" => "韓国",
    "kw" => "クゥェート",
    "ky" => "ケイマン諸島",
    "kz" => "カザフスタン",
    "la" => "ラオ人民民主共和国",
    "lb" => "レバノン共和国",
    "lc" => "セントルシア",
    "li" => "リヒテンシュタイン公国",
    "lk" => "スリランカ民主社会主義共和国",
    "lr" => "リベリア共和国",
    "ls" => "レソト王国",
    "lt" => "リトアニア",
    "lu" => "ルクセンブルク大公国",
    "lv" => "ラトビア共和国",
    "ly" => "リビア",
    "ma" => "モロッコ",
    "mc" => "モナコ",
    "md" => "モルドバ共和国",
    "mg" => "マダガスカル",
    "mh" => "マーシャル諸島",
    "mk" => "マケドニア",
    "ml" => "マリ",
    "mm" => "ミャンマー",
    "mn" => "モンゴル",
    "mo" => "マカオ",
    "mp" => "北マリアナ諸島",
    "mq" => "マルティニーク島",
    "mr" => "モーリタニア・イスラム共和国",
    "ms" => "モントセラト",
    "mt" => "マルタ共和国",
    "mu" => "モーリシャス共和国",
    "mv" => "モルディブ共和国",
    "mw" => "マラウイ共和国",
    "mx" => "メキシコ",
    "my" => "マレーシア連邦",
    "mz" => "モザンビーク人民共和国",
    "na" => "ナミビア共和国",
    "nc" => "ニューカレドニア",
    "ne" => "ニジェール共和国",
    "nf" => "ノーフォーク島",
    "ng" => "ナイジェリア連邦共和国",
    "ni" => "ニカラグア共和国",
    "nl" => "オランダ王国",
    "no" => "ノルウェイ",
    "np" => "ネパール",
    "nr" => "ナウル共和国",
    "nu" => "ニウエ",
    "nz" => "ニュージーランド",
    "om" => "オマン",
    "pa" => "パナマ",
    "pe" => "ペルー",
    "pf" => "仏領ポリネシア",
    "pg" => "パプアニューギニア",
    "ph" => "フィリピン",
    "pk" => "パキスタン",
    "pl" => "ポーランド",
    "pm" => "サンピエール・エ・ミクロン",
    "pn" => "ピトケアン",
    "pr" => "プエルトリコ",
  "ps" => "パレスチナ自治政府",
    "pt" => "ポルトガル",
    "pw" => "パラオ",
    "py" => "パラグアイ",
    "qa" => "カタール",
    "re" => "レユニオン島",
    "ro" => "ルーマニア",
    "ru" => "ロシア連邦",
    "rs" => "ロシア",
    "rw" => "ルワンダ",
    "sa" => "サウジアラビア",
    "sb" => "ソロモン諸島",
    "sc" => "セーシェル共和国",
    "sd" => "スーダン",
    "se" => "スウェーデン",
    "sg" => "シンガポール",
    "sh" => "セントヘレナ",
    "si" => "スロベニア共和国",
    "sj" => "スバールバル諸島",
    "sk" => "スロヴァキア",
    "sl" => "シエラレオネ共和国",
    "sm" => "サンマリノ共和国",
    "sn" => "セネガル共和国",
    "so" => "ソマリア民主共和国",
    "sr" => "スリナム共和国",
    "st" => "サントメプリンシペ共和国",
    "su" => "旧ソビエト連邦",
    "sv" => "エルサルバドル",
    "sy" => "シリアアラブ共和国",
    "sz" => "スイス",
    "tc" => "タークスアンドカイコス諸島",
    "td" => "チャド",
    "tf" => "フランスの南部テリトリ",
    "tg" => "トーゴ共和国",
    "th" => "タイ王国",
    "tj" => "タジキスタン共和国",
    "tk" => "トケラウ",
    "tm" => "トルクメニスタン",
    "tn" => "チュニジア共和国",
    "to" => "トンガ",
    "tp" => "東ティモール",
    "tr" => "トルコ共和国",
    "tt" => "トリニダード・トバゴ共和国",
    "tv" => "ツバル",
    "tw" => "中国の台湾および州",
    "tz" => "タンザニア連合共和国",
    "ua" => "ウクライナ",
    "ug" => "ウガンダ",
    "uk" => "イギリス",
    "gb" => "グレートブリテン",
    "um" => "アメリカ島嶼部",
    "us" => "アメリカ合衆国",
    "uy" => "ウルグアイ東方共和国",
    "uz" => "ウズベキスタン共和国",
    "va" => "バチカン市国",
    "vc" => "セントヴィンセント・グレナディン",
    "ve" => "ベネズエラ",
    "vg" => "バージン群島、英国",
    "vi" => "バージン群島、米国",
    "vn" => "ベトナム社会主義共和国",
    "vu" => "バヌアツ共和国",
    "wf" => "ウォリス・フツナ",
    "ws" => "サモア",
    "ye" => "イエメン",
    "yt" => "マヨット",
    "yu" => "ユーゴスラビア",
    "za" => "南アフリカ",
    "zm" => "ザンビア",
    "zr" => "ザイール",
    "zw" => "ジンバブエ",
    "com" => "-",
    "net" => "-",
    "org" => "-",
    "edu" => "-",
    "int" => "-",
    "arpa" => "-",
    "gov" => "-",
    "mil" => "-",
    "reverse" => "-",
    "biz" => "-",
    "info" => "-",
    "name" => "-",
    "pro" => "-",
    "coop" => "-",
    "aero" => "-",
    "museum" => "-",
    "tv" => "ツバル",
    "ws" => "サモア",
);
 if (isset($lang[$code])) {
   return $lang[$code];
 } else {
  return false;
 }
}