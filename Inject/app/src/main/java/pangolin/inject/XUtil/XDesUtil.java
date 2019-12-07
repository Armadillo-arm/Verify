package pangolin.inject.XUtil;

import java.security.Key;
import java.security.spec.AlgorithmParameterSpec;

import javax.crypto.Cipher;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.DESKeySpec;
import javax.crypto.spec.IvParameterSpec;

public class XDesUtil {
    public static byte[] encode(byte[] key, String data) {
        try {
            return encode(key, hexStr2ByteArr(data));
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }
    public static byte[] decode(byte[] key, String data) {
        try {
            return decode(key, hexStr2ByteArr(data));
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }
    public static byte[] hexStr2ByteArr(String strIn) throws Exception
    {
        byte[] arrB = strIn.getBytes();
        int iLen = arrB.length;
        byte[] arrOut = new byte[iLen / 2];
        for (int i = 0; i < iLen; i = i + 2)
        {
            String strTmp = new String(arrB, i, 2);
            arrOut[i / 2] = (byte)Integer.parseInt(strTmp, 16);
        }
        return arrOut;
    }
    public static byte[] encode(byte[] key, byte[] data) throws Exception {
        try {
            DESKeySpec dks = new DESKeySpec(key);
            SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("DES");
            Key secretKey = keyFactory.generateSecret(dks);
            Cipher cipher = Cipher.getInstance("DES");
            IvParameterSpec iv = new IvParameterSpec("12345678".getBytes());
            AlgorithmParameterSpec paramSpec = iv;
            cipher.init(Cipher.ENCRYPT_MODE, secretKey, paramSpec);
            byte[] bytes = cipher.doFinal(data);
            return bytes;
        } catch (Exception e) {
            throw new Exception(e);
        }
    }
    public static byte[] decode(byte[] key, byte[] data) throws Exception {
        try {
            DESKeySpec dks = new DESKeySpec(key);
            SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("DES");
            Key secretKey = keyFactory.generateSecret(dks);
            Cipher cipher = Cipher.getInstance("DES");
            IvParameterSpec iv = new IvParameterSpec("12345678".getBytes());
            AlgorithmParameterSpec paramSpec = iv;
            cipher.init(Cipher.DECRYPT_MODE, secretKey, paramSpec);
            byte[] bytes = cipher.doFinal(data);
            return bytes;
        } catch (Exception e) {
            throw new Exception(e);
        }
    }
}
