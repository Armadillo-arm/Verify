package pangolin.inject.XBase;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.widget.Toast;

import androidx.annotation.IdRes;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;

import pangolin.inject.XApp;
import pangolin.inject.XView.XLoading;

public abstract class XBaseActivity extends AppCompatActivity {
    protected abstract void X_Init();

    protected abstract int X_Layout();

    private XLoading xLoading;
    private SharedPreferences mSharedPreferences;
    private SharedPreferences.Editor mSharedPreferencesEditor;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(X_Layout());
        X_Init();
    }

    public <T extends View> T X_Id(@IdRes int id) {
        return findViewById(id);
    }

    public void X_StartActivity(Class<?> clz, boolean finish) {
        startActivity(new Intent(this, clz));
        if (finish) finish();
    }

    public void ShowToast(final String msg) {
        if (Looper.myLooper() == Looper.getMainLooper())
            Toast.makeText(this, msg, Toast.LENGTH_LONG).show();
        else
            new Handler(Looper.getMainLooper()).post(new Runnable() {
                @Override
                public void run() {
                    Toast.makeText(XApp.getContext(), msg, Toast.LENGTH_LONG).show();
                }
            });
    }

    public void ShowLoading(String Title) {
        xLoading = new XLoading();
        xLoading.setCancelable(false);
        xLoading.show(getSupportFragmentManager(), "Loading");
        xLoading.setTille(Title);
    }

    public void HideLoading() {
        if (xLoading != null)
            xLoading.dismiss();
    }

    public SharedPreferences.Editor getPreferencesEditor(String name)
    {
        mSharedPreferences=getSharedPreferences(name, Context.MODE_PRIVATE);
        mSharedPreferencesEditor=mSharedPreferences.edit();
        return mSharedPreferencesEditor;
    }
    public SharedPreferences getPreferences(String name)
    {
        mSharedPreferences=getSharedPreferences(name, Context.MODE_PRIVATE);
        return mSharedPreferences;
    }
}
