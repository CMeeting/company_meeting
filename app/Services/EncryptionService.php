<?php


namespace App\Services;


class EncryptionService
{
    private $rsa_private_webview = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCb9LJZzBAOR3YDT0/rLqY1pCu6
ge7lb0aU4k/aTnxSz10t/JUUtGMvQ2tBy36hjwulouM6xoFSm8gnjovALuVpTltRPPEfN+uX6AVy
Wa58TsS2+Q9JRPHJlOb4sDEriUnzLtonahqF/S/EiijMAkNc2b1KNNUb2bDNmLi6YvT7d1sh3iIf
kTggsyt3UVUjh2exEVpb8Ww4grmLJjFssi5nZksLDhlA0MUr4dPmIcDl0RlIwHTsxrJbewnX7kNd
MsSvCxYV1EtmBx9KRCu8ON2bRwKaaWd7fAT9yZWYpjQmbZ5A4CAqDcEAlW8RrhMv0FNKVn1/GMVu
DC/PsWTyI1fJAgMBAAECggEAazkHThmwiEC1cH0dEzLVb1faALbIitHAkygtgognjb+n7rATx1Jm
Rt5xZaEFBQqWFWEyHPzRePxF1nx4tL8JVydaPTZLCQ2nWp9Y70+dWTXGpY4C5nsSSWeojR6GD8qq
Hxn9PtACQFDcgKIEfo6nxbSmIdjFFArNNe1kRDE5AZHtZi7Q6JDRewp6mESrSGED1JodQ4xJoEZl
1HPvMTxcGGQk/8EZOWXaGvGuBEfIMc33g61YxxhWXb6Rf7x8QKpa2cRLbqN+74wdiWyQUAcSjZSi
LzeY0a4h7DKkhBEHWXT4svrXsF1RjMRpTi8amxNgUP7RSSTiF9iT6fpGpnwWKQKBgQDsP18lwMjl
blt8mKqrL9SKVMoOLKUaci9WNV14qEUgNp/RIqjflS0wasbRo7suix2Cwd58ZVAmueuH5W9ZM9NP
A9RP5vnm8fqfqMZIq+Z7ORhry5QpGlOo0tKkik716kTRXl5w3O/YcR8wBZ7Xzwh467OYcRMNnr2i
8F5eT4Ny5wKBgQCo/sQrc4KqsYwdWWZW+O92jIylxgtCQsZNg1cXql91IuB3lx1HnDaN/R2MeCkp
ok+hYXWB0848321x+vYdL9ivLUrnHpNoycFEuhqNTYbyZWnq1tKPz9RDhPIwEmlQVW3dU9hWEjF5
6sa4NXP7/lSiYaQe0FS4BYQ7KCwahxw5zwKBgQCyCYv92oxLguBFRJQR0EjfOsjO58xA4t5TnIHl
hQIbJa9JH1KetEBK5Z0Rrc9JYyOJP+sYWg2OMbyTPE+urMTJHjypPosEBUX3fmx3pbxbDosd6Vyt
oR588uw1fdnFr5fPJMwNSCMkPRPgz8uGHYrn/dDpyAUw1XlaSsUDU+7e7wKBgDHCej6VD3d1Od12
cmTPfar5bXxuWOhUwxEVyDImr17NOyzbaPqp64JZcjbnNq+UpD/Pc4sbs5FG9HHoREmJyhKikniF
D0B+j5LN/X13w3FiHRLR2EQSHN0YqzsuYKYP0X6m7n9BciIE2ChrTNB5LbB2cZ3FF/zrWukURhA1
kkEPAoGBAIpoB6ehuqTn7cisbXjaLqwzX35y50+KED2TamB1FkTjfU5YPsSnKBX9CfCeEd+yr08r
mecHPUYVj5SpLlcpzx+n7taJCzNJ4SuE186qwHR+MGAS3reXsz5l5GVu7WSH2uKZ7OuXnzCJC4/r
dx/f2C0wOi6fdC46fAZVACROoV9T';

    private $rsa_public_webview = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAm/SyWcwQDkd2A09P6y6mNaQruoHu5W9G
lOJP2k58Us9dLfyVFLRjL0NrQct+oY8LpaLjOsaBUpvIJ46LwC7laU5bUTzxHzfrl+gFclmufE7E
tvkPSUTxyZTm+LAxK4lJ8y7aJ2oahf0vxIoozAJDXNm9SjTVG9mwzZi4umL0+3dbId4iH5E4ILMr
d1FVI4dnsRFaW/FsOIK5iyYxbLIuZ2ZLCw4ZQNDFK+HT5iHA5dEZSMB07MayW3sJ1+5DXTLErwsW
FdRLZgcfSkQrvDjdm0cCmmlne3wE/cmVmKY0Jm2eQOAgKg3BAJVvEa4TL9BTSlZ9fxjFbgwvz7Fk
8iNXyQIDAQAB';


    private $rsa_private_saas = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCErusw0fy2i17ETb1T5QYEMqKN
MWwxUmTmmV9YVjln12wJ29XuWESFELEqxcqQ747JuxR+ja+1w6jeR9XrRg9C9yTz6aJMzAj0Qhn3
QKfpZEEALRtxzG4Zm1TOgzfl5MS58sq0b69PqJ9ddkcObrZWxN2X/gqL4kaK1rmGtig1CxAXeop2
VlD3YsuQYK/bWknOjq7G0vlPuWzhAZ1p54xFG0D8r2FxnskSMDvvBk9Fi+Y2+p7NK/WTZJG7eLg5
WpuIUCBe9laKU3ryTVTxqO3KerRrQSIbvobPamGyohUY8+cigAW7uPOslATGiF+o7v1USxO+a0Hi
GKtAxqc03BFVAgMBAAECggEADUwadgIVeZXuIW5JEydYuwL4F35kgWzof9PPImNBzY5hXXuSrRhb
hZpupwK7klrvzHfbTfOVMHozalN4tOOIlAWAxpEbmua33wLg506rhVBu0z2yCm2aeyZ+EVRFPdz3
ektC1WJ8B1e24P8BkAi4bWm9a4YiOHKs7y0l2sJWtToJpzQI9Q1cz5HaldSk+mIjlLBrEFxNurCs
XVAbs63n8+y1EayqNPmUokt0hxg01AmLWWOzHnoPEeNHh6gqID1mafqyKzMfbqE4TwIn6AjpBP5G
EkRK7hE9ICXtoTq2oYAaC1mpt8pa50Mdz/ZP9Ozw/sahjHf8Ii8r3u9kXaUOAQKBgQC8uTK689Ru
0GE/Mou2yJNF8hGJZCtWNXFW3zb/40I+WD0VPEsFq71c6e3BARfJXpVOiK1A47nH6j7PhR9O5rjp
eQhqC0oBpDhrgzGfRpRafHcn115+US9p1+laIDm2lGp9Okogyx3jO+kCtdq4NrdrBBBYqA732W2Y
1fqKMJ0+ZQKBgQCz+4Z6p13zu8s1Yepg8hI9547t2hrFcQdD7BoWrJ/cqkUH9LWXRASYSK9Zd0Hv
wPw6pUvxoJdhRKbLc8noNzXcLctyvrsyOBZsqGLYgZPBOSZaWrsmCbHpdpr/L4yLkQ8LSulNV1iK
65mpE/KRfi/bceuEZiAV4FC4sDRxUnSgMQKBgQCSF9eyq8B+TCeL0aggRiLj2aFDt1xo7xQfCMq9
ygJk2s/mWXdGG42KmLhpWVw9kU+JrPpZqjAOF0KqSMZUwPzSF9Swqq6OmL7oU3Ydc3iyl3lySSGo
LVbD29WT5DBpK3lIGdB6lGI9nwohBAAd7JWObWXuC6MSsKlL0BGT2lBPlQKBgCU0KgEylYYrXH8F
ZXhysXigY/ggrIYBrjnJFT94oXbwr2JMoDjLc5Oy+qQotbWEk97fikpBIumVUlEd9y+MMhL4WGS4
rmNcGq6NRBly94LqeFYXCTZ8rI0Q2ktDne6/pnd0HbPCCWh1+QZtPCf9CJF5A/ETVzCOdMEokz6V
h8wRAoGAep1avZ120YjKh0W0TfVR6YPuLrbWbBrPR1CXtrycMumjsyv0F4xpPEBTnSLIqzMXAopU
7vvaPToklN4bR2w8ECUYh8wDiMqquUdet+7NZPHzGrxlgCdoRZelV+aLLTF5oOdE3s+G4FcNjoc9
YPJDBPi0LE8iarZMLH2QmJkLMsM=';

    private $rsa_public_saas = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhK7rMNH8totexE29U+UGBDKijTFsMVJk
5plfWFY5Z9dsCdvV7lhEhRCxKsXKkO+OybsUfo2vtcOo3kfV60YPQvck8+miTMwI9EIZ90Cn6WRB
AC0bccxuGZtUzoM35eTEufLKtG+vT6ifXXZHDm62VsTdl/4Ki+JGita5hrYoNQsQF3qKdlZQ92LL
kGCv21pJzo6uxtL5T7ls4QGdaeeMRRtA/K9hcZ7JEjA77wZPRYvmNvqezSv1k2SRu3i4OVqbiFAg
XvZWilN68k1U8ajtynq0a0EiG76Gz2phsqIVGPPnIoAFu7jzrJQExohfqO79VEsTvmtB4hirQMan
NNwRVQIDAQAB';

    private $rsa_private = '';

    private $rsa_public = '';

    public $key = 'T2EKWNOflrd6ICU5';

    public $iv = 'GM3Faw9kX7CBmojt';

    const PROJECT_1_WEBVIEW = 1;
    const PROJECT_2_SAAS = 2;

    public function __construct($project = self::PROJECT_1_WEBVIEW)
    {
        if($project == self::PROJECT_1_WEBVIEW){
            $this->rsa_private = $this->rsa_private_webview;
            $this->rsa_public = $this->rsa_public_webview;
        }else{
            $this->rsa_private = $this->rsa_private_saas;
            $this->rsa_public = $this->rsa_private_saas;
        }
    }

    public function encryption($data){
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->rsa_public, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        $key = openssl_pkey_get_public($public_key);

        openssl_public_encrypt($data, $result, $key);

        return base64_encode($result);
    }

    public function decrypt($data){
        $private_key =  "-----BEGIN PRIVATE KEY-----\n" . wordwrap($this->rsa_private, 64, "\n", true) . "\n-----END PRIVATE KEY-----";
        $key = openssl_pkey_get_private($private_key);

        openssl_private_decrypt(base64_decode($data), $result, $key);

        return $result;
    }

    /**
     * 获取加盐码
     * str长度除以5取余，余数为0则为4，截取邮箱MD5算法后字符串，从余数开始，长度为 余数加3 的字符串
     * @param $str
     * @return false|string
     */
    public function getSaltCode($str){
        $result = md5($str);
        $str_len = strlen($str);

        $mod = $str_len % 5;
        if($mod == 0){
            $mod = 4;
        }

        $sub_len = $mod + 3;

        return substr($result, $mod, $sub_len);
    }
}