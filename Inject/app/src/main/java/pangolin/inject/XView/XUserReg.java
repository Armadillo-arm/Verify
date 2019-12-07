package pangolin.inject.XView;

import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import java.util.HashMap;
import java.util.Map;

import pangolin.inject.R;
import pangolin.inject.XApi.XNetworkRequest;
import pangolin.inject.XBase.XBaseAlertDialog;
import pangolin.inject.XModel.XBasics;
import pangolin.inject.XModel.XUserInfo;

public class XUserReg extends XBaseAlertDialog {
    private EditText et_username;
    private EditText et_password;
    private EditText et_email;
    private EditText et_repeat_password;
    private Button bt_go;
    private Result<XBasics> Result;

    //注册成功的回调接口
    public interface Result<T extends XBasics> {
        void next(T data, Map<String, String> Map);

        void error(Throwable throwable);
    }

    public void setResultListener(Result<XBasics> data) {
        Result = data;
    }

    @Override
    protected int X_Layout() {
        return R.layout.dialog_reg;
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
                String pass2 = et_repeat_password.getText().toString();
                String email = et_email.getText().toString();
                if (user.isEmpty() || pass.isEmpty() || pass2.isEmpty() || email.isEmpty()) return;
                if (!pass.equals(pass2)) {
                    ShowToast("两次密码不正确");
                    return;
                }
                if (!email.contains("@")) {
                    ShowToast("邮箱格式不正确");
                    return;
                }
                final Map<String, String> map = new HashMap<>();
                map.put("UserName", user);
                map.put("PassWord", pass);
                map.put("Email", email);
                ShowLoading("注册中......");
                XNetworkRequest.UserReg(map, new XNetworkRequest.Result<XBasics>() {
                    @Override
                    public void next(XBasics data) {
                        HideLoading();
                        if (Result != null)
                            Result.next(data, map);
                    }

                    @Override
                    public void error(Throwable throwable) {
                        HideLoading();
                        Result.error(throwable);
                    }
                });
            }
        });
    }

    private void XInti() {
        et_username = X_Id(R.id.et_username);
        et_password = X_Id(R.id.et_password);
        et_email = X_Id(R.id.et_email);
        et_repeat_password = X_Id(R.id.et_repeatpassword);
        bt_go = X_Id(R.id.bt_go);
    }
}
