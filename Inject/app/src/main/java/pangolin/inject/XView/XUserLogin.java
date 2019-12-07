package pangolin.inject.XView;

import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.TextView;


import java.util.HashMap;
import java.util.Map;

import pangolin.inject.R;
import pangolin.inject.XApi.XNetworkRequest;
import pangolin.inject.XBase.XBaseAlertDialog;
import pangolin.inject.XModel.XBasics;
import pangolin.inject.XModel.XUserInfo;

public class XUserLogin extends XBaseAlertDialog {
    private EditText et_username;
    private EditText et_password;
    private Button bt_go;
    private TextView bt_reg;
    public CheckBox bt_jz;
    private Result<XUserInfo> Result;

    //登录成功的回调接口
    public interface Result<T extends XBasics> {
        void next(T data);

        void error(Throwable throwable);

        void reg();
    }

    public void setResultListener(Result<XUserInfo> data) {
        Result = data;
    }

    @Override
    protected int X_Layout() {
        return R.layout.dialog_login;
    }

    @Override
    protected void X_Call() {
        XInti();
        XListener();
    }

    private void XListener() {
        bt_go.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String user = et_username.getText().toString();
                String pass = et_password.getText().toString();
                if (user.isEmpty() || pass.isEmpty()) return;
                Map<String, String> map = new HashMap<>();
                map.put("UserName", user);
                map.put("PassWord", pass);
                ShowLoading("登录中......");
                XNetworkRequest.UserLogin(map, new XNetworkRequest.Result<XUserInfo>() {
                    @Override
                    public void next(XUserInfo data) {
                        HideLoading();
                        data.data.password = pass;
                        if (Result != null)
                            Result.next(data);
                    }

                    @Override
                    public void error(Throwable throwable) {
                        Result.error(throwable);
                        HideLoading();
                    }
                });
            }
        });
        bt_reg.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Result.reg();
            }
        });
    }

    private void XInti() {
        et_username = X_Id(R.id.et_username);
        et_password = X_Id(R.id.et_password);
        bt_go = X_Id(R.id.bt_go);
        bt_reg = X_Id(R.id.reg);
        bt_jz = X_Id(R.id.jz);
    }

    public void setData(Map<String, String> Map) {
        for (Map.Entry<String, String> entry : Map.entrySet()) {
            String mapKey = entry.getKey();
            String mapValue = entry.getValue();
            if (mapKey.equals("UserName"))
                et_username.setText(mapValue);
            if (mapKey.equals("PassWord"))
                et_password.setText(mapValue);
        }
    }
}
