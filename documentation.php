<?php include_once('header.php'); ?>
<body>
<?php include_once('menu.php'); ?>

<div class="container">
    <h1>How to use JSONme?</h1>
    <h2>File and template</h2>
    <p>
        At first, you will need to choose your Excel (.xls) file by clicking on the first field <span class="fake-btn">Select a file...</span>.
    </p>
    <p>
        Actually, we are supporting only one type of template available, which is called <span class="fake-btn">Simple vertical Excel</span> and represent a file with a list of lines with the same columns.
        The first line of your sheet will need to be the titles of your columns, they will be used to create our structure.
    </p>
    <p>
        When you are ready, click on <span class="fake-btn">Send the file</span>.
    </p>
    <h2>Building your JSON structure</h2>
    <p>Now we will define the structure we want to generate.</p>
    <h3>Ordering columns</h3>
    <p>
        The first level element will be an array containing each line of the sheet as an object.
    </p><p>
        Example:
    </p>
    <pre>
        [
            {
                "column1": "My first line",
                "column2": "Details 1",
                "column3": "Other details 1"
            },
            {
                "column1": "My second line",
                "column2": "Details 2",
                "column3": "Other details 2"
            }
            ...
        ]
    </pre>
    <h3>Include columns in objects</h3>
    <p>
        You can drag'n drop the columns (named from the first line of your file) to reorder them, or even put a column into another.<br/>
        When a column is in another, the parent column will be generated as an object and the child as a property of this object, and the value of the parent will be put in a property called <span class="fake-label">label</span>.<br/>
    </p><p>
        Example:
    </p>
    <pre>
       Structure:

        (tag icon) - country
        - (tag icon) - city
        - - (tag icon) - title
        - - (tag icon) - date

        Generating;

        [
            {
                "country": {
                    "label": "canada",
                    "city": {
                        "label": "montreal"
                        "title": "Title 1",
                        "date": "03/12/2015"
                    }
                }
            },
            {
                "country": {
                    "label": "france",
                    "city": {
                        "label": "paris"
                        "title": "Title 2",
                        "date": "21/12/2015"
                    }
                }
            },
            {
                "country": {
                    "label": "canada",
                    "city": {
                        "label": "vancouver"
                        "title": "Title 3",
                        "date": "23/12/2015"
                    }
                }
            }
        ]
    </pre>
    <h3>Regroupe lines by column values</h3>
    <p>
        If you want to regroup lines by a column value, you can click on the <span class="fake-element"><img src="assets/images/tag-16.png"/> tag icon</span> to change it to a <span class="fake-element"><img src="assets/images/lock-16.png"/> lock icon</span>.<br/>
        It will then contains all datas (from the columns put as children) from the lines with the same value in this column.
    </p><p>
        For example, if we want to regroup all the cities in the same country in a sheet containing this columns: title, date, country, city.
    </p>
    <pre>
         Structure:

        (lock icon) - country
        - (tag icon) - city
        - - (tag icon) - title
        - - (tag icon) - date

        Generating;

        [
            {
                "country": {
                    "id": "canada",
                    "label": "canada",
                    "city": [{
                        "label": "montreal"
                        "title": "Title 1",
                        "date": "03/12/2015",
                    }, {
                        "label": "vancouver"
                        "title": "Title 3",
                        "date": "23/12/2015",
                    }]
                },
                {
                    "id": "france",
                    "label": "france",
                    "city": [{
                        "label": "paris"
                        "title": "Title 2",
                        "date": "21/12/2015",
                    }]
                }
            },

        ]
    </pre>
    <h3>Editing labels</h3>
    <p>
        You can also choose to create, rename or delete the labels.<br/>
        To do that, you need to right click and choose your action.<br/>
        By creating a new label, you can includes the columns into this one.<br/>
        Renaming your columns will allow you to have other key's name for your generated JSON file.
    </p>
    <h3>First row to start</h3>
    <p>
        The first line is generally used for the column's titles of the sheet, so the default value is 2.<br/>
        If you have more than one line for your column's titles or don't want to start directly on your first, you can change this value.
    </p>
    <h3>Geolocation</h3>
    <p>
        JSONme integrate a geolocation tool which will allow you to get the latitude and longitude of an address.<br/>
        To do that, you will need to rename your columns with this names and structure :<br/>
    </p>
    <pre>
        - root element (need to be locked, will regroup cities by element with the same value)
        - - cities (column with the city name)
        - - - locations (created label)
        - - - - street (column with the street)
        - - - - postal_code (column with the postal code)
    </pre>
    <p>
        This will generate 2 new property in the location: <span class="fake-label">lat</span> and <span class="fake-label">lng</span>.
    </p>
    <h2>Generate your JSON file</h2>
    <p>
        The last thing to do is to click on the button <span class="fake-btn">Convert this sheet</span>.<br/>
        Then, you will find in your JSONme project a folder named <span class="fake-label">json</span> containing the file <span class="fake-label">output.json</span> with your generated JSON datas.
    </p>
    <h2>Need more help or found a bug?</h2>
    <p>
        You can ask question, give feedback or create issue tickets on the Gitlab project: <a href="http://git.lemieuxbedard.ca/lemieux-bedard/jsonme">http://git.lemieuxbedard.ca/lemieux-bedard/jsonme</a>
    </p>
</div>
</body>
</html>