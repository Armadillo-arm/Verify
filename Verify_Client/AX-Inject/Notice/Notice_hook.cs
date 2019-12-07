using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Android.App;
using Android.Content;
using Android.Database;
using Android.Net;
using Android.OS;
using Android.Runtime;
using Android.Views;
using Android.Widget;

namespace AX_Inject.Notice
{
    //[ContentProvider(authorities:new string[] {"PanGolin.Notice"},Exported = false)]
    public class Notice_hook : ContentProvider,Application.IActivityLifecycleCallbacks
    {

        public void OnActivityCreated(Activity activity, Bundle savedInstanceState)
        {
            Toast.MakeText(Context, activity.Class.SimpleName, ToastLength.Long).Show();
        }

        public void OnActivityDestroyed(Activity activity)
        {
            
        }

        public void OnActivityPaused(Activity activity)
        {
            
        }

        public void OnActivityResumed(Activity activity)
        {
            
        }

        public void OnActivitySaveInstanceState(Activity activity, Bundle outState)
        {
            
        }

        public void OnActivityStarted(Activity activity)
        {
            
        }

        public void OnActivityStopped(Activity activity)
        {
            
        }










        public override int Delete(Android.Net.Uri uri, string selection, string[] selectionArgs)
        {
            return 0;
        }

        public override string GetType(Android.Net.Uri uri)
        {
            return null;
        }

        public override Android.Net.Uri Insert(Android.Net.Uri uri, ContentValues values)
        {
            return null;
        }


        public override bool OnCreate()
        {
            ((Application)Context).RegisterActivityLifecycleCallbacks(this);
            return true;
        }

        public override ICursor Query(Android.Net.Uri uri, string[] projection, string selection, string[] selectionArgs, string sortOrder)
        {
            return null;
        }

        public override int Update(Android.Net.Uri uri, ContentValues values, string selection, string[] selectionArgs)
        {
            return 0;
        }
    }
}