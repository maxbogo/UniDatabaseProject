<?php

class DatabaseHelper
{
    // Since the connection details are constant, define them as const
    // We can refer to constants like e.g. DatabaseHelper::username
    const username = ''; // use a + your matriculation number
    const password = ''; // use your oracle db password
    const con_string = 'lab';

    // Since we need only one connection object, it can be stored in a member variable.
    // $conn is set in the constructor.
    protected $conn;

    // Create connection in the constructor
    public function __construct()
    {
        try {
            // Create connection with the command oci_connect(String(username), String(password), String(connection_string))
            // The @ sign avoids the output of warnings
            // It could be helpful to use the function without the @ symbol during developing process
            $this->conn = @oci_connect(
                DatabaseHelper::username,
                DatabaseHelper::password,
                DatabaseHelper::con_string
            );

            //check if the connection object is != null
            if (!$this->conn) {
                // die(String(message)): stop PHP script and output message:
                die("DB error: Connection can't be established!");
            }

        } catch (Exception $e) {
            die("DB error: {$e->getMessage()}");
        }
    }

    // Used to clean up
    public function __destruct()
    {
        // clean up
        @oci_close($this->conn);
    }



// MEMBER
    public function selectFromMemberWhere($mem_id, $nickname, $country)
    {
        // Define the sql statement string
        // Notice that the parameters $person_id, $surname, $name in the 'WHERE' clause
        $sql = "SELECT * FROM member
            WHERE mem_id LIKE '%{$mem_id}%'
              AND lower(nickname) LIKE lower('%{$nickname}%')
              AND lower(country) LIKE lower('%{$country}%')
            ORDER BY mem_id";

        $statement = @oci_parse($this->conn, $sql);
        @oci_execute($statement);
        @oci_fetch_all($statement, $res, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        @oci_free_statement($statement);

        return $res;
    }


    public function insertIntoMember($nickname, $country)
    {
        $sql = "INSERT INTO MEMBER (NICKNAME, COUNTRY) VALUES ('@{$nickname}', '{$country}')";

        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        @oci_free_statement($statement);
        return $success;
    }


    public function updateMember($mem_id, $country)
    {
        $sql = "UPDATE MEMBER SET country = '{$country}' WHERE mem_id = {$mem_id}";

        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        @oci_free_statement($statement);
        return $success;
    }


    public function deleteMember($mem_id)
    {
        $errorcode = 0;


        $sql = 'BEGIN P_DELETE_MEMBER(:mem_id, :errorcode); END;';
        $statement = @oci_parse($this->conn, $sql);

        @oci_bind_by_name($statement, ':mem_id', $mem_id);
        @oci_bind_by_name($statement, ':errorcode', $errorcode);

        @oci_execute($statement);

        @oci_free_statement($statement);

        return $errorcode;
    }



// REVIEW
    public function insertIntoReview($points, $date_rev, $mem_id, $wine_id)
    {
        $sql = "INSERT INTO review (points, date_rev, member_id, wine_id) 
                VALUES ({$points}, to_date('{$date_rev}','YYYY-MM-DD'), {$mem_id}, {$wine_id})";


        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        // Error print
        /*if (!$success) {
            $e = oci_error($statement);  // For oci_execute errors pass the statement handle
            print htmlentities($e['message']);
            print "\n<pre>\n";
            print htmlentities($e['sqltext']);
            printf("\n%".($e['offset']+1)."s", "^");
            print  "\n</pre>\n";
        }*/
        @oci_free_statement($statement);
        return $success;
    }


    public function deleteReview($review_id)
    {
        $errorcode = 0;


        $sql = 'BEGIN P_DELETE_REVIEW(:review_id, :errorcode); END;';
        $statement = @oci_parse($this->conn, $sql);

        @oci_bind_by_name($statement, ':review_id', $review_id);
        @oci_bind_by_name($statement, ':errorcode', $errorcode);

        @oci_execute($statement);
        @oci_free_statement($statement);

        //$errorcode == 1 => success
        //$errorcode != 1 => Oracle SQL related errorcode;
        return $errorcode;
    }

    public function updateReview($review_id, $points)
    {
        $sql = "UPDATE REVIEW SET points = '{$points}' WHERE review_id = {$review_id}";

        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        @oci_free_statement($statement);
        return $success;
    }

    public function selectFromReviewWhere($review_id, $points, $date_rev, $mem_id, $wine_id)
    {
        $sql = "SELECT * FROM review
            WHERE review_id LIKE '%{$review_id}%'
              AND points LIKE '%{$points}%'
              AND date_rev LIKE '%{$date_rev}%'
              AND member_id LIKE '%{$mem_id}%'
              AND wine_id LIKE '%{$wine_id}%'
            ORDER BY review_id";

        $statement = @oci_parse($this->conn, $sql);
        @oci_execute($statement);
        @oci_fetch_all($statement, $res, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        @oci_free_statement($statement);

        return $res;
    }


// WANT TRY
    public function selectFromWantTryWhere($mem_id, $wine_id)
    {
        $sql = "SELECT * FROM want_try
            WHERE member_id LIKE '%{$mem_id}%'
              AND wine_id LIKE '%{$wine_id}%'
            ORDER BY member_id";

        $statement = @oci_parse($this->conn, $sql);
        @oci_execute($statement);
        @oci_fetch_all($statement, $res, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        @oci_free_statement($statement);

        return $res;
    }

    public function updateWantTry($mem_id, $wine_id, $wine_id_new)
    {
        $sql = "UPDATE want_try SET wine_id = '{$wine_id_new}' 
                WHERE member_id = {$mem_id} AND wine_id = {$wine_id}";

        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        @oci_free_statement($statement);
        return $success;
    }

    public function deleteWantTry($mem_id, $wine_id)
    {
        $errorcode = 0;


        $sql = 'BEGIN P_DELETE_WANT_TRY(:member_id, :wine_id, :errorcode); END;';
        $statement = @oci_parse($this->conn, $sql);

        @oci_bind_by_name($statement, ':member_id', $mem_id);
        @oci_bind_by_name($statement, ':wine_id', $wine_id);
        @oci_bind_by_name($statement, ':errorcode', $errorcode);

        @oci_execute($statement);
        @oci_free_statement($statement);

        return $errorcode;
    }

    public function insertIntoWantTry($mem_id, $wine_id)
    {
        $sql = "INSERT INTO want_try (member_id, wine_id) 
                VALUES ({$mem_id}, {$wine_id})";


        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        // Error print
        /*if (!$success) {
            $e = oci_error($statement);  // For oci_execute errors pass the statement handle
            print htmlentities($e['message']);
            print "\n<pre>\n";
            print htmlentities($e['sqltext']);
            printf("\n%".($e['offset']+1)."s", "^");
            print  "\n</pre>\n";
        }*/
        @oci_free_statement($statement);
        return $success;
    }




// WINE
    public function insertIntoWine($color, $vintage, $winery_name, $grape_name)
    {
        $sql = "INSERT INTO wine (color, vintage, winery_name, grape_name) 
                VALUES ('{$color}', {$vintage}, '{$winery_name}', '{$grape_name}')";


        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        // Error print
        if (!$success) {
            $e = oci_error($statement);  // For oci_execute errors pass the statement handle
            print htmlentities($e['message']);
            print "\n<pre>\n";
            print htmlentities($e['sqltext']);
            printf("\n%".($e['offset']+1)."s", "^");
            print  "\n</pre>\n";
        }
        @oci_free_statement($statement);
        return $success;
    }


    public function deleteWine($wine_id)
    {
        $errorcode = 0;


        $sql = 'BEGIN P_DELETE_WINE(:wine_id, :errorcode); END;';
        $statement = @oci_parse($this->conn, $sql);

        @oci_bind_by_name($statement, ':wine_id', $wine_id);
        @oci_bind_by_name($statement, ':errorcode', $errorcode);

        @oci_execute($statement);
        @oci_free_statement($statement);

        return $errorcode;
    }

    public function updateWine($wine_id, $vintage)
    {
        $sql = "UPDATE WINE SET vintage = '{$vintage}' WHERE wine_id = {$wine_id}";

        $statement = @oci_parse($this->conn, $sql);
        $success = @oci_execute($statement) && @oci_commit($this->conn);
        @oci_free_statement($statement);
        return $success;
    }

    public function selectFromWineWhere($wine_id, $color, $vintage, $winery_name, $grape_name)
    {
        $sql = "SELECT * FROM wine
            WHERE wine_id LIKE '%{$wine_id}%'
              AND lower(color) LIKE lower('%{$color}%')
              AND vintage LIKE '%{$vintage}%'
              AND lower(winery_name) LIKE lower('%{$winery_name}%')
              AND lower(grape_name) LIKE lower('%{$grape_name}%')
            ORDER BY wine_id";

        $statement = @oci_parse($this->conn, $sql);
        @oci_execute($statement);
        @oci_fetch_all($statement, $res, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        @oci_free_statement($statement);

        return $res;
    }
}