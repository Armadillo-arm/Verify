package pangolin.inject.XView;

import android.view.Gravity;

import androidx.appcompat.widget.AppCompatTextView;

import pangolin.inject.R;
import pangolin.inject.XBase.XBaseAlertDialog;

public class XLoading extends XBaseAlertDialog {
    private AppCompatTextView mTitle;
    private String Title;
    @Override
    protected int X_Layout() {
        return R.layout.dialog_loading;
    }

    @Override
    protected void X_Call() {
        mTitle = X_Id(R.id.loading_Title);
        mTitle.setText(Title);
    }

    public void setTille(String title) {
            this.Title=title;
    }
}
