package pangolin.inject.XModel;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class XAdsInfo extends XBasics {
    @SerializedName(value = "data")
    public List<data> data;

    public class data {
        @SerializedName(value = "id")
        public int id;
        @SerializedName(value = "ads_name")
        public String ads_name;
        @SerializedName(value = "ads_title")
        public String ads_title;
        @SerializedName(value = "ads_url")
        public String ads_url;
        @SerializedName(value = "ads_img")
        public String ads_img;
        @SerializedName(value = "ads_time")
        public String ads_time;
    }
}
