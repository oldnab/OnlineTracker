For french version, see [README.fr.md](README.fr.md).
# 1. Objective  
This project aims to enable the use of OsmAnd's online tracking feature. It includes two components:  
1. A "recorder" **set.php** to be configured in OsmAnd to save all tracking points sent by the app.  
2. A "viewer" **suivre.php** allowing others to follow the current position of the OsmAnd user, displaying the full route on OpenStreetMap since tracking began, along with an elevation profile.  

# 2. Licenses  
This is an open-source project under the MIT license.  
Icons used in the project are royalty-free and sourced from Flaticon, with the requirement to credit the creators. The **suivre.php** page complies with this obligation. Any copy of these icon files must also include proper credit.  

# 3. Implementation  
## 3.1 Installation  
You need a web server where the project files are deployed (two PHP files, one CSS file, and five PNG icons). The server must support PHP and allow file creation within the PHP directory (for tracking data storage).  

## 3.2 Usage  

### a) Logging tracking data sent by OsmAnd  
In OsmAnd, under the profile settings, go to "Trip recording" > "Online tracking":  

* Enable online tracking (otherwise, nothing will be sent).  
* Set the "Web address" parameter to:  
> *your_website_url*/set.php?id=xxxxxx&cmd=add&lat={0}&lon={1}&tim={2}&alt={4}

Key notes:  

* The identity (`id=xxxxxx`, e.g., the user's first name) links the recorded data with the tracking feature. You can have multiple phones with OsmAnd, each configured with a different ID. This allows tracking a specific device.
* The identity can only contain ASCII letters (A–Z, a–z) and digits (0–9).
* The file created on the web server will be named `YYYY-MM-DD-xxxxx`, meaning one file per day. There's no need to update OsmAnd's configuration daily. However, if you do one walk in the morning and another in the afternoon, both will be appended to the same file (the break will be visible in the tracking view). To avoid this, you have two options:
    * Change the "Web address" in the afternoon to use a different ID for the second walk.
    * Reset the tracking file before starting the afternoon walk. This will erase any previously collected data for that day. To do so, use a web browser and go to:
> *your_website_url*/set.php?id=xxxxxx&**cmd=new**

* Other parameters provided by OsmAnd (hdop, speed, bearing, etc.) are not recorded by `set.php`.

### b) Real-time tracking  
Anyone who wants to follow your location can use a web browser and go to:  
> *your_website_url*/suivre.php?id=xxxxxx

This feature shows the route taken from the first recorded point of the day on an OpenStreetMap background. It:  

* Allows zooming into map areas of interest  
* Includes a **start icon** (small flag) showing the start time (click the icon)  
* Includes a **last point icon** (large red pin) showing (on click) the timestamp, total distance traveled, and elevation gain  
* May include **pause icons** (small green pins) indicating identified rest stops  
* Includes a **target icon** (bottom-right corner) that re-centers and zooms on the latest point  
* Includes a **mountain icon** (bottom-right corner) that toggles a window with the elevation profile up to the last recorded point. To close this window, click the small cross in the top-right corner or click the mountain icon again.  
* Automatically refreshes every 10 minutes (but can be manually refreshed through standard browser functions)

You can also view a track from a previous day using:  
> *your_website_url*/suivre.php?id=xxxxxx,dat=YYYY-MM-DD  

# Limitations and Issues (to be expanded)  

## Privacy  
Anyone with the URL and the ID can track or even add points.  
This could be addressed in a future version by adding password protection.  

## Smoothing of irregular GPS points  
`suivre.php` performs internal smoothing of positions. This can be configured (possibly exposed in the tracking URL in a future version). It’s useful for walking tracks, where GPS precision can be poor, resulting in erratic paths. It’s less impactful for vehicle tracking.  

## Smoothing of elevation data  
`suivre.php` also performs internal smoothing of elevation values. This too can be configured (possibly in a future version via the URL). This smoothing is important to get more meaningful elevation gain (D+).  

## Day change  
A hike crossing midnight (server time) causes a new tracking file to be created...  

## Time zones  
It’s unclear whether the displayed timestamps are accurate when the server and the users of `set.php` and `suivre.php` are in different time zones.
