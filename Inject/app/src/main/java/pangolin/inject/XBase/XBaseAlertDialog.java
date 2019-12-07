package pangolin.inject.XBase;

import android.app.Dialog;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.Toast;

import androidx.annotation.IdRes;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.DialogFragment;
import androidx.fragment.app.FragmentManager;

import pangolin.inject.XApp;
import pangolin.inject.XView.XLoading;


public abstract class XBaseAlertDialog extends DialogFragment {
    protected abstract int X_Layout();

    protected abstract void X_Call();

    private View mView;
    private AlertDialog mDialog;
    private XLoading xLoading;
    @NonNull
    @Override
    public Dialog onCreateDialog(@Nullable Bundle savedInstanceState) {
        mView = LayoutInflater.from(getActivity()).inflate(X_Layout(), null);
        mDialog = new AlertDialog.Builder(getActivity()).setView(mView).create();
        X_Call();
        return mDialog;
    }

    public <T extends View> T X_Id(@IdRes int id) {
        return mView.findViewById(id);
    }

    public void onStart() {
        super.onStart();
        getDialog().getWindow().setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));
    }

    @Nullable
    @Override
    public Dialog getDialog() {
        return mDialog;
    }

    public void ShowToast(final String msg) {
        if (Looper.myLooper() == Looper.getMainLooper())
            Toast.makeText(getActivity(), msg, Toast.LENGTH_LONG).show();
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
        xLoading.show(getActivity().getSupportFragmentManager(), "Loading");
        xLoading.setTille(Title);
    }

    public void HideLoading() {
        if (xLoading != null)
            xLoading.dismiss();
    }
}
