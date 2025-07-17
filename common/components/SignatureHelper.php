<?php

namespace common\components;

use Yii;

class SignatureHelper
{
    public static function encryptAndEncode($base64Data, $userId)
    {
        // Step 1: Compress
        $compressed = gzcompress($base64Data);

        // Step 2: Encrypt
        $encrypted = Yii::$app->security->encryptByPassword($compressed, $userId);

        // Step 3: Base64 encode for DB storage
        return base64_encode($encrypted);
    }

    public static function decodeAndDecrypt($encodedEncryptedData, $userId)
    {
        // Step 1: Decode from DB
        $encrypted = base64_decode($encodedEncryptedData);

        // Step 2: Decrypt
        $compressed = Yii::$app->security->decryptByPassword($encrypted, $userId);

        if ($compressed === false) {
            return null; // Decryption failed
        }

        // Step 3: Uncompress
        return gzuncompress($compressed);
    }
}
