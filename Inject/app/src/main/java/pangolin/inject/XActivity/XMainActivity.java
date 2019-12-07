package pangolin.inject.XActivity;

import android.Manifest;
import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.Toolbar;
import androidx.core.app.ActivityCompat;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.google.android.material.bottomappbar.BottomAppBar;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.material.navigation.NavigationView;
import com.google.gson.Gson;
import com.sunsky.marqueeview.MarqueeView;
import com.youth.banner.Banner;
import com.youth.banner.BannerConfig;
import com.youth.banner.listener.OnBannerListener;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Random;

import pangolin.inject.R;
import pangolin.inject.XAdapter.XModelAdapter;
import pangolin.inject.XApi.XNetworkRequest;
import pangolin.inject.XBase.XBaseActivity;
import pangolin.inject.XConstant;
import pangolin.inject.XModel.XAdsInfo;
import pangolin.inject.XModel.XBasics;
import pangolin.inject.XModel.XUserInfo;
import pangolin.inject.XUtil.XApkSign;
import pangolin.inject.XUtil.XTimeUtil;
import pangolin.inject.XUtil.XTransformerRandom;
import pangolin.inject.XView.XGlideImageLoader;
import pangolin.inject.XView.XUserLogin;
import pangolin.inject.XView.XUserReg;

public class XMainActivity extends XBaseActivity implements
        NavigationView.OnNavigationItemSelectedListener,
        View.OnClickListener, XModelAdapter.OnItemClick,
        SwipeRefreshLayout.OnRefreshListener,
        OnBannerListener {
    private FloatingActionButton fab;
    private NavigationView navigationView;
    private DrawerLayout drawer;
    private Banner mBanner;
    private Toolbar toolbar;
    private RecyclerView mRecyclerView;
    //Header头
    private View Header;
    private AppCompatImageView Header_Image;
    private TextView UserInfo;

    //模块
    private SwipeRefreshLayout mSwipeRefreshLayout;
    private XModelAdapter mXModelAdapter;
    private List<String> list;

    //广告流
    private List<String> Ads_Title;
    private List<String> Ads_Img;
    private List<String> Ads_Url;

    //公告流
    private MarqueeView mMarqueeView;

    @Override
    protected void X_Init() {
        Init();
        InitData();
    }

    private void InitData() {
        //初始化广告数据
        mBanner.setOnBannerListener(this);
        XNetworkRequest.GetAds(new XNetworkRequest.Result<XAdsInfo>() {
            @Override
            public void next(XAdsInfo data) {
                Ads_Title = new ArrayList<>();
                Ads_Img = new ArrayList<>();
                Ads_Url = new ArrayList<>();
                for (XAdsInfo.data info : data.data) {
                    if (XTimeUtil.strToDateLong(info.ads_time).getTime() < System.currentTimeMillis())
                        continue;
                    Ads_Title.add(info.ads_title);
                    Ads_Img.add(info.ads_img);
                    Ads_Url.add(info.ads_url);
                }
                mBanner.setBannerTitles(Ads_Title);
                mBanner.setImages(Ads_Img);
                mBanner.start();
            }

            @Override
            public void error(Throwable throwable) {
                mBanner.setVisibility(View.GONE);
            }
        });
        //初始化模块信息
        list = new ArrayList<>();
        for (int i = 0; i < 25; i++) list.add("" + i);
        mXModelAdapter = new XModelAdapter(list);
        mRecyclerView.setAdapter(mXModelAdapter);
        mXModelAdapter.setOnItemClickListener(this);
        //初始化用户信息
        if (!getPreferences("user").getString("UserName", "").isEmpty()) {
            Map<String, String> Map = new HashMap<>();
            Map.put("UserName", getPreferences("user").getString("UserName", ""));
            Map.put("PassWord", getPreferences("user").getString("PassWord", ""));
            XNetworkRequest.UserLogin(Map, new XNetworkRequest.Result<XUserInfo>() {
                @Override
                public void next(XUserInfo data) {
                    if (data.code == 200) {
                        XConstant.setxUserInfo(data);
                        UserInfo.setText("用户名:" + data.data.username + "\n"
                                + "邮箱:" + data.data.email + "\n"
                                + "到期时间:" + data.data.expire_time + "\n"
                                + "登录次数:" + data.data.login_count);
                    }
                }

                @Override
                public void error(Throwable throwable) {
                    ShowToast("自动登录失败");
                }
            });
        }
        //初始化公告
        List<View> views=new ArrayList<>();
        for (int i = 0; i < 25; i++){
            TextView v=new TextView(this);
            v.setText(list.get(i));
            views.add(v);
        }
        mMarqueeView.setViews(views);
    }

    private void Init() {
        //初始化标题栏和底部导航栏
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        BottomAppBar bar = findViewById(R.id.bar);
        setSupportActionBar(bar);
        //初始化Bug反馈按钮
        fab = X_Id(R.id.fab);
        fab.setOnClickListener(this);
        //初始化模块表格
        mRecyclerView = X_Id(R.id.viewlist);
        mRecyclerView.setLayoutManager(new GridLayoutManager(this, 2));
        mRecyclerView.setHasFixedSize(true);
        mSwipeRefreshLayout = X_Id(R.id.Refresh);
        mSwipeRefreshLayout.setOnRefreshListener(this);
        //初始化侧滑
        drawer = X_Id(R.id.drawer_layout);
        navigationView = X_Id(R.id.nav_view);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(this, drawer, bar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawer.addDrawerListener(toggle);
        toggle.syncState();
        navigationView.setNavigationItemSelectedListener(this);
        //初始化Header头
        Header = navigationView.getHeaderView(0);
        Header_Image = Header.findViewById(R.id.header_image);
        UserInfo = Header.findViewById(R.id.UserInfo);
        UserInfo.setOnClickListener(this);
        Header_Image.setOnClickListener(this);
        //动画旋转
        rotateImageAlbum();
        //初始化广告控件
        mBanner = X_Id(R.id.banner);
        mBanner.setBannerStyle(BannerConfig.CIRCLE_INDICATOR_TITLE);
        mBanner.setImageLoader(new XGlideImageLoader());
        mBanner.setBannerAnimation(XTransformerRandom.RandomTransformer());
        mBanner.setDelayTime(5000);
        mBanner.setIndicatorGravity(BannerConfig.CENTER);
        //初始化公告控件
        mMarqueeView=X_Id(R.id.upview);
        //权限申请
        ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE}, 100);
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == 100) {
            if (grantResults.length > 0) {
                List<String> deniedPermissions = new ArrayList<>();
                for (int i = 0; i < grantResults.length; i++) {
                    int result = grantResults[i];
                    if (result != PackageManager.PERMISSION_GRANTED) {
                        String permission = permissions[i];
                        deniedPermissions.add(permission);
                    }
                    if (!deniedPermissions.isEmpty()) {
                        ActivityCompat.requestPermissions(this, new String[]{android.Manifest.permission.WRITE_EXTERNAL_STORAGE}, 100);
                    }
                }
            }
        }
    }

    private void rotateImageAlbum() {
        fab.animate().setDuration(100).rotation(fab.getRotation() + 2f).setListener(new AnimatorListenerAdapter() {
            @Override
            public void onAnimationEnd(Animator animation) {
                rotateImageAlbum();
                super.onAnimationEnd(animation);
            }
        });
        Header_Image.animate().setDuration(100).rotation(Header_Image.getRotation() + 2f).setListener(new AnimatorListenerAdapter() {
            @Override
            public void onAnimationEnd(Animator animation) {
                rotateImageAlbum();
                super.onAnimationEnd(animation);
            }
        });
    }

    @Override
    protected int X_Layout() {
        return R.layout.activity_main;
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = X_Id(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            super.onBackPressed();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.action_login) {
            Login_Reg();
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public boolean onNavigationItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.nav_sign:
                XApkSign.SignApk(new File("sdcard/a.apk"));
                break;
            case R.id.nav_group:
                startActivity(new Intent("android.intent.action.VIEW", Uri.parse("mqqapi://card/show_pslcard?src_type=internal&version=1&uin=" + XConstant.getxConfInfo().group + "&card_type=group&source=qrcode")));
                break;
            case R.id.nav_link:
                startActivity(new Intent("android.intent.action.VIEW", Uri.parse(XNetworkRequest.GetUrl().toString())));
                break;
            case R.id.nav_pay:
                break;
        }
        drawer.closeDrawer(GravityCompat.START);
        return true;
    }

    @Override
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.header_image:
            case R.id.UserInfo:
                if (XConstant.getxUserInfo() != null)
                    Login_Reg();
                else
                    ShowToast("你已登录 无需重复登录");
                break;
        }
    }

    //登录/注册
    private void Login_Reg() {
        final XUserLogin xUserLogin = new XUserLogin();
        xUserLogin.show(getSupportFragmentManager(), "Login");
        xUserLogin.setResultListener(new XUserLogin.Result<XUserInfo>() {
            @Override
            public void next(XUserInfo data) {
                ShowToast(data.msg);
                if (data.code == 200) {
                    XConstant.setxUserInfo(data);
                    UserInfo.setText("用户名:" + data.data.username + "\n"
                            + "邮箱:" + data.data.email + "\n"
                            + "到期时间:" + data.data.expire_time + "\n"
                            + "登录次数:" + data.data.login_count);
                    xUserLogin.dismiss();
                    if (xUserLogin.bt_jz.isChecked())
                        getPreferencesEditor("user")
                                .putString("UserName", data.data.username)
                                .putString("PassWord", data.data.password)
                                .apply();
                }
            }

            @Override
            public void error(Throwable throwable) {
                ShowToast("服务器连接失败 请检测你的网络连接情况");
            }

            @Override
            public void reg() {
                final XUserReg xUserReg = new XUserReg();
                xUserReg.show(getSupportFragmentManager(), "Reg");
                xUserReg.setResultListener(new XUserReg.Result<XBasics>() {
                    @Override
                    public void next(XBasics data, Map<String, String> Map) {
                        ShowToast(data.msg);
                        if (data.code == 200) {
                            xUserReg.dismiss();
                            xUserLogin.setData(Map);
                        }
                    }

                    @Override
                    public void error(Throwable throwable) {
                        ShowToast("服务器连接失败 请检测你的网络连接情况");
                    }
                });
            }
        });
    }

    //模块点击事件
    @Override
    public void OnClick(int position) {
        ShowToast("" + position);
    }

    //模块数据刷新
    @Override
    public void onRefresh() {
        list.clear();
        Random random = new Random();
        for (int i = 0; i < random.nextInt(30); i++) list.add("数据刷新 " + i);
        mXModelAdapter.notifyDataSetChanged();
        mSwipeRefreshLayout.setRefreshing(false);
    }

    //广告点击跳转
    @Override
    public void OnBannerClick(int position) {
        if (Ads_Url != null)
            startActivity(new Intent("android.intent.action.VIEW", Uri.parse(Ads_Url.get(position))));
    }

    @Override
    public void onResume() {
        super.onResume();
        mMarqueeView.startFlipping();
    }

    @Override
    public void onPause() {
        super.onPause();
        mMarqueeView.stopFlipping();
    }
}
