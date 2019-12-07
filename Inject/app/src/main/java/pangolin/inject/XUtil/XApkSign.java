package pangolin.inject.XUtil;

import android.content.Context;

import com.android.apksig.ApkSigner;
import com.android.apksig.apk.ApkFormatException;
import com.google.common.collect.ImmutableList;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.security.InvalidKeyException;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.PrivateKey;
import java.security.Security;
import java.security.SignatureException;
import java.security.UnrecoverableKeyException;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;

import pangolin.inject.XApp;
import sun1.security.provider.JavaProvider;

public class XApkSign {
    public static void SignApk(File Input){
        try {
            KeyStore keyStore=KeyStore.getInstance("JKS");
            keyStore.load(XApp.getContext().getAssets().open("Sign.JKS"),"PanGolin".toCharArray());
            String alias=keyStore.aliases().nextElement();
            PrivateKey privateKey= (PrivateKey) keyStore.getKey(alias,"PanGolin".toCharArray());
            X509Certificate x509Certificate= (X509Certificate) keyStore.getCertificate(alias);
            ApkSigner.SignerConfig signerConfig =
                    new ApkSigner.SignerConfig.Builder(
                            "穿山甲云科技", privateKey, ImmutableList.of(x509Certificate))
                            .build();
            ApkSigner ApkSigner=new ApkSigner.Builder(ImmutableList.of(signerConfig))
                    .setCreatedBy("穿山甲云科技")
                    .setInputApk(Input)
                    .setOutputApk(new File("sdcard"+File.separator+(Input.getName().replace(".apk","_Sign.apk"))))
                    .setMinSdkVersion(15)
                    .setV1SigningEnabled(true)
                    .setV2SigningEnabled(true)
                    .setV3SigningEnabled(true)
                    .setDebuggableApkPermitted(true)
                    .build();
            ApkSigner.sign();
        } catch (IOException e) {
            e.printStackTrace();
        } catch (ApkFormatException e) {
            e.printStackTrace();
        } catch (NoSuchAlgorithmException e) {
            e.printStackTrace();
        } catch (InvalidKeyException e) {
            e.printStackTrace();
        } catch (SignatureException e) {
            e.printStackTrace();
        }catch (KeyStoreException e) {
            e.printStackTrace();
        } catch (CertificateException e) {
            e.printStackTrace();
        } catch (UnrecoverableKeyException e) {
            e.printStackTrace();
        }
    }
}
