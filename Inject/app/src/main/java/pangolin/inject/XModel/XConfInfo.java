package pangolin.inject.XModel;

import com.google.gson.annotations.SerializedName;

public class XConfInfo extends XBasics {
    @SerializedName(value = "fkw")
    public String fkw;
    @SerializedName(value = "share_msg")
    public String share_msg;
    @SerializedName(value = "group")
    public String group;
    @SerializedName(value = "qq")
    public String qq;
}
