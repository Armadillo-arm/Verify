package pangolin.inject.XModel;

import com.google.gson.annotations.SerializedName;

public class XUserInfo extends XBasics {
    @SerializedName(value = "data")
    public data data;

    public class data {
        @SerializedName(value = "username")
        public String username;
        @SerializedName(value = "login_count")
        public int login_count;
        @SerializedName(value = "email")
        public String email;
        @SerializedName(value = "expire_time")
        public String expire_time;
        @SerializedName(value = "password")
        public String password;
    }
}
