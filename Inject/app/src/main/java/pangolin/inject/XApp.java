package pangolin.inject;

import android.app.Application;
import android.content.Context;

import java.security.Security;

import sun1.security.provider.JavaProvider;

public class XApp extends Application {
    public static Context context;
    public static Context getContext(){
        return context;
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
    }

    @Override
    public void onCreate() {
        super.onCreate();
        context=this;
        Security.addProvider(new JavaProvider());
    }
}
