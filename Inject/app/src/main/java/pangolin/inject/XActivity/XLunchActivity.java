package pangolin.inject.XActivity;

import android.graphics.Color;
import android.os.Build;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;

import androidx.annotation.RequiresApi;

import com.bumptech.glide.Glide;
import com.google.gson.Gson;

import pangolin.inject.R;
import pangolin.inject.XApi.XNetworkRequest;
import pangolin.inject.XBase.XBaseActivity;
import pangolin.inject.XConstant;
import pangolin.inject.XModel.XConfInfo;
import pangolin.inject.XModel.XNoticeInfo;

public class XLunchActivity extends XBaseActivity {
    @RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
    @Override
    protected void X_Init() {
        getWindow().setFlags( WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN );
        Glide.with(this).load("https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1569046032&di=c660b62bde9f450671c55c1445bc8d9c&src=http://searchfoto.ru/img/xyygpKbDS1y8pTjXWy83VS8rMS9fLSy3RL8mwz0yx9fcM0AtI9vW1KEq2yI4MSC4NM3DXrXI09HP0SPQNV0vMLbAutzUyNgCzMmwNzSGsomJbQzCjIDnHNgUMwNx8W1OIMNBoQz1DAA.jpg").into((ImageView)X_Id(R.id.lunch));
        XNetworkRequest.GetConfInfo(new XNetworkRequest.Result<XConfInfo>() {
            @Override
            public void next(XConfInfo data) {
                XConstant.setxConfInfo(data);
                if (data != null && data.code == 200)
                    new Handler(Looper.getMainLooper()).postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            X_StartActivity(XMainActivity.class, true);
                        }
                    }, 2000);
            }

            @Override
            public void error(Throwable throwable) {
                ShowToast("初始化失败 请检测网络连接是否正常");
            }
        });
    }

    @Override
    protected int X_Layout() {
        return R.layout.activity_lunch;
    }
}
