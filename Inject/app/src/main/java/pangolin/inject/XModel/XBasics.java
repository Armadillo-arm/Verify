package pangolin.inject.XModel;

import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.lang.reflect.ParameterizedType;
import java.lang.reflect.Type;

public class XBasics implements Serializable {
    @SerializedName(value = "code")
    public int code;
    @SerializedName(value = "msg")
    public String msg;
}
