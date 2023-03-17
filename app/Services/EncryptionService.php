<?php


namespace App\Services;


class EncryptionService
{
    private $rsa_private = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCb9LJZzBAOR3YDT0/rLqY1pCu6
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

    private $rsa_public = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAm/SyWcwQDkd2A09P6y6mNaQruoHu5W9G
lOJP2k58Us9dLfyVFLRjL0NrQct+oY8LpaLjOsaBUpvIJ46LwC7laU5bUTzxHzfrl+gFclmufE7E
tvkPSUTxyZTm+LAxK4lJ8y7aJ2oahf0vxIoozAJDXNm9SjTVG9mwzZi4umL0+3dbId4iH5E4ILMr
d1FVI4dnsRFaW/FsOIK5iyYxbLIuZ2ZLCw4ZQNDFK+HT5iHA5dEZSMB07MayW3sJ1+5DXTLErwsW
FdRLZgcfSkQrvDjdm0cCmmlne3wE/cmVmKY0Jm2eQOAgKg3BAJVvEa4TL9BTSlZ9fxjFbgwvz7Fk
8iNXyQIDAQAB';

    public $key = 'T2EKWNOflrd6ICU5';

    public $iv = 'GM3Faw9kX7CBmojt';

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