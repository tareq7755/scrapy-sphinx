<!DOCTYPE html>
<html>
    <head>
        <title>Bayt job search</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    </head>
    <body>
        <div class="container">

            <div class="jumbotron">
                <h1>Use Sphinx to search for jobs</h1>
                <p>
                    Fill in the form fields to search for a job
                </p>
            </div>  

            <div class="row">

                <div class="col-md-12">
                    <form action="baytSearch.php" method="GET">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">                    
                                    <input type="text" class="form-control" name="industry" placeholder="Industry">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">                    
                                    <input type="text" class="form-control" name="role" placeholder="Role">
                                </div>                
                            </div>
                        </div>

                        <button type="submit" class="btn btn-default">Search</button>

                    </form>
                </div>

            </div>
            <br />
            <?php
            if (!empty($_GET)) {
                $params = $_GET;
                $searchQuery = '';

                //construct the query with respect the user's parameters
                foreach ($params as $key => $param) {
                    if (!empty($param)) {
                        $searchQuery .= '@' . $key . ' ' . $param . ' ';
                    }
                }

                //include the sphinx php api and perform a search query according to the search params
                require_once 'sphinxapi.php';
                $sphinxClient = new SphinxClient();
                $sphinxClient->SetServer("localhost", 9312);
                $sphinxClient->SetMatchMode(SPH_MATCH_EXTENDED);
                $sphinxClient->SetRankingMode(SPH_RANK_SPH04);
                $searchResult = $sphinxClient->Query($searchQuery);

                //iff there are results, connect to the database and get the searched jobs data
                if (!empty($searchResult) && isset($searchResult['matches'])) {
                    $matches = array_keys($searchResult['matches']);
                    $pgConnection = pg_connect("host=localhost dbname=bayt user=tareq password=tareq123");

                    //if connection to DB is ok get the data
                    if (pg_connection_status($pgConnection) === PGSQL_CONNECTION_OK) {
                        $data = [];
                        $recordIds = implode(',', $matches);

                        $query = "SELECT * FROM job WHERE id IN ($recordIds)";
                        $result = pg_query($pgConnection, $query);


                        while ($row = pg_fetch_assoc($result)) {
                            ?>
                            <div class="row">                        
                                <div class="col-md-2">
                                    <label>Vacancy URL : </label> <a href="<?= $row['job_url'] ?>">Click me</a>
                                </div>

                                <div class="col-md-4">
                                    <label>Title : </label> <small><?= $row['title'] ?></small>
                                </div>

                                <div class="col-md-4">
                                    <label>Compnay : </label> <small><?= $row['company'] ?></small>
                                </div>

                                <div class="col-md-2">
                                    <label>Industry : </label> <small><?= $row['industry'] ?></small>
                                </div>
                            </div>
                            <hr>
                        <?php }
                        ?>
                        <?php
                    }
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        No results found!
                    </div>
                <?php } ?>

                <div class = "row">

                </div>
            <?php } ?>
        </div>

    </body>
</html>