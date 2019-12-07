package pangolin.inject.XModel;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class XNoticeInfo extends XBasics {
    @SerializedName(value = "data")
    public List<data> data;

    public class data{
        @SerializedName(value = "id")
        public int id;
        @SerializedName(value = "title")
        public String title;
        @SerializedName(value = "content")
        public String content;
        @SerializedName(value = "creat_time")
        public String creat_time;
    }
}
