<?php
/*
 * encode decode function
 */
class funCrypt {
    static $lockstream = 'st=lDEFABCNOPyzghi_jQRST-UwxkVWXYZabcdef+IJK6/7nopqr89LMmGH012345uv';
    public function enCrypt($txtStream,$password){
        $lockLen = strlen(self::$lockstream);
        $lockCount = rand(0,$lockLen-1);
        $randomLock = self::$lockstream[$lockCount];
        $password = md5($password.$randomLock);
        $txtStream = base64_encode($txtStream);
        $tmpStream = '';
        $i=0;$j=0;$k = 0;
        for ($i=0; $i<strlen($txtStream); $i++) {
            $k = $k == strlen($password) ? 0 : $k;
            $j = (strpos(self::$lockstream,$txtStream[$i])+$lockCount+ord($password[$k]))%($lockLen);
            $tmpStream .= self::$lockstream[$j];
            $k++;
        }
        return $tmpStream.$randomLock;
    }
    public function deCrypt($txtStream,$password){
        $lockLen = strlen(self::$lockstream);
        $txtLen = strlen($txtStream);
        $randomLock = $txtStream[$txtLen - 1];
        $lockCount = strpos(self::$lockstream,$randomLock);
        $password = md5($password.$randomLock);
        $txtStream = substr($txtStream,0,$txtLen-1);
        $tmpStream = '';
        $i=0;$j=0;$k = 0;
        for ($i=0; $i<strlen($txtStream); $i++) {
            $k = $k == strlen($password) ? 0 : $k;
            $j = strpos(self::$lockstream,$txtStream[$i]) - $lockCount - ord($password[$k]);
            while($j < 0){
                $j = $j + ($lockLen);
            }
            $tmpStream .= self::$lockstream[$j];
            $k++;
        }
        return base64_decode($tmpStream);
    }
}
?>
