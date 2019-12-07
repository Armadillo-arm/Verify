package pangolin.inject.XApi;

import android.os.Handler;
import android.os.Looper;

import com.google.gson.Gson;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.Proxy;
import java.net.ProxySelector;
import java.net.SocketAddress;
import java.net.URI;
import java.net.URL;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import pangolin.inject.XModel.XConfInfo;
import pangolin.inject.XModel.XBasics;
import pangolin.inject.XModel.XNoticeInfo;
import pangolin.inject.XModel.XAdsInfo;
import pangolin.inject.XModel.XUserInfo;
import pangolin.inject.XUtil.XDesUtil;
import pangolin.inject.XUtil.XMD5;

public class XNetworkRequest {
    //回调接口
    public interface Result<T extends XBasics> {
        void next(T data);

        void error(Throwable throwable);
    }

    //实例化OkHttpClient
    private static OkHttpClient getOkHttpClient() {
        return new OkHttpClient.Builder().proxySelector(new ProxySelector() {
            @Override
            public List<Proxy> select(URI uri) {
                return Collections.singletonList(Proxy.NO_PROXY);
            }

            @Override
            public void connectFailed(URI uri, SocketAddress sa, IOException ioe) {

            }
        }).build();
    }

    //实例化Request
    private static Request getRequest(Map<String, String> Map) {
        Request.Builder Request = new Request.Builder();
        Request.url(GetUrl() + "/Auth/Verify");
        Request.addHeader("Key",""+System.currentTimeMillis());
        for (Map.Entry<String, String> entry : Map.entrySet()) {
            String mapKey = entry.getKey();
            String mapValue = entry.getValue();
            Request.addHeader(mapKey, mapValue);
        }
        return Request.build();
    }

    //获取服务器地址Url
    public static URL GetUrl() {
        try {
            URL url = new URL("http://192.168.101.103");
            return url;
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }
        return null;
    }

    //取配置信息
    public static void GetConfInfo(final Result<XConfInfo> Result) {
        Map<String, String> map = new HashMap<>();
        map.put("Api", "PanGolin_GetConfInfo");
        final Request Request = getRequest(map);
        getOkHttpClient().newCall(Request).enqueue(new Callback() {

            @Override
            public void onFailure(Call p1, final IOException p2) {
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.error(p2);
                    }
                });
            }

            @Override
            public void onResponse(Call p1, final Response p2) throws IOException {
                p2.body().close();
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.next(new Gson().fromJson(new String(XDesUtil.decode(XMD5.md5(Request.header("Key")).getBytes(), p2.headers().get("Result"))), XConfInfo.class));
                    }
                });
            }
        });
    }

    //取服务器公告
    public static void GetNotice(final Result<XNoticeInfo> Result) {
        Map<String, String> map = new HashMap<>();
        map.put("Api", "PanGolin_GetNotice");
        final Request Request = getRequest(map);
        getOkHttpClient().newCall(Request).enqueue(new Callback() {

            @Override
            public void onFailure(Call p1, final IOException p2) {
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.error(p2);
                    }
                });
            }

            @Override
            public void onResponse(Call p1, final Response p2) throws IOException {
                p2.body().close();
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.next(new Gson().fromJson(new String(XDesUtil.decode(XMD5.md5(Request.header("Key")).getBytes(), p2.headers().get("Result"))), XNoticeInfo.class));
                    }
                });
            }
        });
    }

    //取广告
    public static void GetAds(final Result<XAdsInfo> Result) {
        Map<String, String> map = new HashMap<>();
        map.put("Api", "PanGolin_GetAds");
        final Request Request = getRequest(map);
        getOkHttpClient().newCall(Request).enqueue(new Callback() {

            @Override
            public void onFailure(Call p1, final IOException p2) {
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.error(p2);
                    }
                });
            }

            @Override
            public void onResponse(Call p1, final Response p2) throws IOException {
                p2.body().close();
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.next(new Gson().fromJson(new String(XDesUtil.decode(XMD5.md5(Request.header("Key")).getBytes(), p2.headers().get("Result"))), XAdsInfo.class));
                    }
                });
            }
        });
    }

    //用户登录
    public static void UserLogin(Map<String, String> Map, final Result<XUserInfo> Result) {
        Map<String, String> usermap = new HashMap<>();
        usermap.put("Api", "PanGolin_UserLogin");
        usermap.putAll(Map);
        final Request Request = getRequest(usermap);
        getOkHttpClient().newCall(Request).enqueue(new Callback() {

            @Override
            public void onFailure(Call p1, final IOException p2) {
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.error(p2);
                    }
                });
            }

            @Override
            public void onResponse(Call p1, final Response p2) throws IOException {
                p2.body().close();
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.next(new Gson().fromJson(new String(XDesUtil.decode(XMD5.md5(Request.header("Key")).getBytes(), p2.headers().get("Result"))), XUserInfo.class));
                    }
                });
            }
        });
    }

    //用户注册
    public static void UserReg(Map<String, String> Map, final Result<XBasics> Result) {
        Map<String, String> usermap = new HashMap<>();
        usermap.put("Api", "PanGolin_CreateUser");
        usermap.putAll(Map);
        final Request Request = getRequest(usermap);
        getOkHttpClient().newCall(Request).enqueue(new Callback() {

            @Override
            public void onFailure(Call p1, final IOException p2) {
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.error(p2);
                    }
                });
            }

            @Override
            public void onResponse(Call p1, final Response p2) throws IOException {
                p2.body().close();
                new Handler(Looper.getMainLooper()).post(new Runnable() {
                    @Override
                    public void run() {
                        Result.next(new Gson().fromJson(new String(XDesUtil.decode(XMD5.md5(Request.header("Key")).getBytes(), p2.headers().get("Result"))), XBasics.class));
                    }
                });
            }
        });
    }
}
