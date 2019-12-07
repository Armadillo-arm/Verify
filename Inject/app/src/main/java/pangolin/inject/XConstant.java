package pangolin.inject;

import pangolin.inject.XModel.XConfInfo;
import pangolin.inject.XModel.XUserInfo;

public class XConstant {
    private static XConfInfo xConfInfo;
    private static XUserInfo xUserInfo;

    public static void setxConfInfo(XConfInfo xConfInfo) {
        XConstant.xConfInfo = xConfInfo;
    }

    public static void setxUserInfo(XUserInfo xUserInfo) {
        XConstant.xUserInfo = xUserInfo;
    }

    public static XConfInfo getxConfInfo() {
        return xConfInfo;
    }

    public static XUserInfo getxUserInfo() {
        return xUserInfo;
    }
}
