using System;
using System.Text;
using Javax.Crypto.Spec;
using Javax.Crypto;
using Android.Util;
using System.Security.Cryptography;
using System.IO;

namespace AX_Inject.AuthDialog.util
{
    public class Des
    {
        public static string encrypt(string data, string mykey)
        {
            MemoryStream ms = new MemoryStream();
            DESCryptoServiceProvider des = new DESCryptoServiceProvider();;
            byte[] datas = Encoding.UTF8.GetBytes(data);
            des.Key = ASCIIEncoding.UTF8.GetBytes(mykey);
            des.IV = ASCIIEncoding.UTF8.GetBytes(mykey);
            des.Padding = PaddingMode.PKCS7;
            des.Mode = System.Security.Cryptography.CipherMode.ECB;
            CryptoStream cs = new CryptoStream(ms, des.CreateEncryptor(), CryptoStreamMode.Write);
            cs.Write(datas, 0, datas.Length);
            cs.FlushFinalBlock();
            return Base64.EncodeToString(ms.ToArray(), Base64Flags.Default);
        }
        public static string decrypt(string pToDecrypt, string sKey)
        {
            MemoryStream ms = new MemoryStream();
            DESCryptoServiceProvider des = new DESCryptoServiceProvider();
            byte[] datas = Base64.Decode(pToDecrypt, Base64Flags.Default);
            des.Key = ASCIIEncoding.UTF8.GetBytes(sKey);
            des.IV = ASCIIEncoding.UTF8.GetBytes(sKey);
            des.Padding = PaddingMode.PKCS7;
            des.Mode = System.Security.Cryptography.CipherMode.ECB;
            CryptoStream cs = new CryptoStream(ms, des.CreateDecryptor(), CryptoStreamMode.Write);
            cs.Write(datas, 0, datas.Length);
            cs.FlushFinalBlock();
            return Encoding.Default.GetString(ms.ToArray());
        }
        public static byte[] Hex2Byte(string byteStr)
        {
            int len = byteStr.Length / 2;
            byte[] data = new byte[len];
            for (int i = 0; i < len; i++)
            {
                data[i] = Convert.ToByte(byteStr.Substring(i * 2, 2), 16);
            }
            return data;
        }
        public static string DesDecrypt(string pToDecrypt, string sKey)
        {
            MemoryStream ms = new MemoryStream();
            DESCryptoServiceProvider des = new DESCryptoServiceProvider();
            byte[] datas = Base64.Decode(pToDecrypt,0);
            des.Key = ASCIIEncoding.UTF8.GetBytes(sKey);
            //des.IV = ASCIIEncoding.UTF8.GetBytes(sKey);
            des.Padding = PaddingMode.PKCS7;
            des.Mode = System.Security.Cryptography.CipherMode.ECB;
            CryptoStream cs = new CryptoStream(ms, des.CreateDecryptor(), CryptoStreamMode.Write);
            cs.Write(datas, 0, datas.Length);
            cs.FlushFinalBlock();
            return Encoding.Default.GetString(ms.ToArray());
        }
    }
}