<?php

class mibClass
{
    private $template;
    private $sql;
    private $frm;
    private $tp;
    private $ns;
    private $sc;
    private $from;
    private $prefs;

    function __construct()
    {
        require_once (e_PLUGIN . 'mib/templates/mib_template.php');
        $this->template = new mibTemplate;
        require_once (e_PLUGIN . 'mib/shortcodes/mib_shortcodes.php');
        $this->sc = new mib_shortcodes;
        $this->prefs = e107::pref('mib'); // returns an array.
        //   print_a($this->prefs);
        e107::js('mib', 'js/mib.js', 'jquery'); // Load Plugin javascript and include jQuery framework
        e107::css('mib', 'css/mib.css'); // load css file
        e107::lan('mib', false, true); // load language file ie. e107_plugins/_blank/languages/English.php
        e107::meta('keywords', 'some words'); // add meta data to <HEAD>
        $this->sql = e107::getDB(); // mysql class object
        $this->tp = e107::getParser(); // parser for converting to HTML and parsing templates etc.
        $this->frm = e107::getForm(); // Form element class.
        $this->ns = e107::getRender(); // render in theme box.

    }
    public function runPage()
    {
        $class = $this->prefs['viewClass']; // get the class to check
        //var_dump(e107::getUser()->checkClass($class, false));
        if (e107::getUser()->checkClass($class, false))
        {
            $this->parseQuery();
            $this->doAction();
        } else
        {
            //  print_a($class);
            $this->notPermitted();
        }
    }
    private function notPermitted()
    {


    }
    private function parseQuery()
    {
        $this->from = 0;
        $this->action = 'list';
        $this->historyFrom = 0;
        $this->id = 0;
        // print_a($this->sc);
        //        print_a($_POST);
        if (isset($_GET['action']))
        {
            $this->from = (int)$_GET['from'];
            $this->action = $_GET['action'];
            $this->historyFrom = (int)$_GET['historyFrom'];
            $this->id = (int)$_GET['id'];
            $this->ajaxid = (int)$_GET['ajaxid'];
            $this->field = $_GET['sortField'];
            $this->sortDirection = $_GET['sortDirection'];

            $this->mibSearchType = $_GET['mibSearchType'];
            $this->mibSearchTown = $_GET['mibSearchTown'];
            $this->mibSearchName = $_GET['mibSearchName'];
        } elseif (isset($_POST['action']))
        {
            $this->from = (int)$_POST['from'];
            $this->action = $_POST['action'];
            $this->historyFrom = (int)$_POST['historyFrom'];
            $this->field = $_POST['sortField'];
            $this->sortDirection = $_POST['sortdirection'];
            $this->mibSearchType = $_POST['mibSearchType'];
            $this->mibSearchTown = $_POST['mibSearchTown'];
            $this->mibSearchName = $_POST['mibSearchName'];
            $this->id = (int)$_POST['id'];
            if (isset($_POST['mibNameSort']))
            {
                $this->field = 'name';
                $this->sortDirection = $_POST['mibNameSort'];
            }
            if (isset($_POST['mibTypeSort']))
            {
                $this->field = 'type';
                $this->sortDirection = $_POST['mibTypeSort'];
            }
            if (isset($_POST['mibTypeSort']))
            {
                $this->field = 'type';
                $this->sortDirection = $_POST['mibTypeSort'];
            }
            if (isset($_POST['mibTownSort']))
            {
                $this->field = 'town';
                $this->sortDirection = $_POST['mibTownSort'];
            }
            if (isset($_POST['mibWillingSort']))
            {
                $this->field = 'willing';
                $this->sortDirection = $_POST['mibWillingSort'];
            }
            if (isset($_POST['mibBottleSort']))
            {
                $this->field = 'bottle';
                $this->sortDirection = $_POST['mibBottleSort'];
            }
        } elseif (isset($_SESSION['mib']))
        {
            // session
        }
        $this->sc->from = $this->from;
        $this->sc->sortField = $this->field;
        $this->sc->action = $this->action;
        $this->sc->historyFrom = $this->historyFrom;
        $this->sc->id = $this->id;
        $this->sc->mibSearchType = $this->mibSearchType;
        $this->sc->mibSearchTown = $this->mibSearchTown;
        $this->sc->mibSearchName = $this->mibSearchName;
        $this->sc->sortDirection = $this->sortDirection;
        $this->sc->order = $this->sortDirection;
        //*
        $this->template->from = $this->from;
        $this->template->action = $this->action;
        $this->template->historyFrom = $this->historyFrom;
        $this->template->id = $this->id;
        //    print_a($this->sc);
        // print "AAA";
    }
    private function doAction()
    {
        // error_reporting(E_ALL);
        switch ($this->action)
        {
            case 'genCsv':

                $text .= $this->generatecsv();

                break;
            case 'csvlist':

                $text .= $this->getcsv();

                break;
            case 'histrec':
                if ($_GET['ajax'] == 'true')
                {
                    echo $this->ajaxHistRec();
                    // echo "Ajaxed";
                }
                exit();
                //  die("QQQ");
                break;
            case 'view':
                $text .= $this->mibView();
                break;
            case 'list':
            default:
                $text .= $this->listAll();
                break;
        }
        return $text;
    }
    function getcsv()
    {

        $text = $this->tp->parseTemplate($this->template->getcsv(), false, $this->sc);
        return $this->ns->tablerender("Message in a Bottle", $text);
    }
    private function generatecsv()
    {
        // create the where clause
        $where = ' WHERE  mib_locations_id > 0 ';
        if ((int)$this->mibSearchType > 0)
        {
            $where .= " AND mib_location_type_fk=$this->mibSearchType ";
            $this->from = 0;
        }
        if (!empty($this->mibSearchTown))
        {
            $where .= " AND mib_location_town like '%$this->mibSearchTown%' ";
            $this->from = 0;
        }
        //create the order
        $order = '';
        if (isset($_POST['mibSort1']) || isset($_POST['mibSort2']))
        {
            $order = " ORDER BY ";
        }

        if (isset($_POST['mibSort1']))
        {
            switch ($_POST['mibSort1'])
            {
                case 'name':
                    $order .= 'mib_location_name';
                    break;
                case 'type':
                    $order .= 'mib_type_name';
                    break;
                case 'town':
                    $order .= 'mib_location_town';
                    break;
                case 'bottles':
                    $order .= 'bottles desc';
                    break;
                case 'visit':
                    $order .= 'lastvisit asc';
                    break;
                case 'willing':
                    $order .= 'mib_location_willing desc';
                    break;
                default:

            }
        }
        if (isset($_POST['mibSort1']) && isset($_POST['mibSort2']))
        {
            $order .= ', ';
        }
        if (isset($_POST['mibSort2']))
        {
            switch ($_POST['mibSort2'])
            {
                case 'name':
                    $order .= 'mib_location_name';
                    break;
                case 'type':
                    $order .= 'mib_type_name';
                    break;
                case 'town':
                    $order .= 'mib_location_town';
                    break;
                case 'bottles':
                    $order .= 'bottles desc';
                    break;
                case 'visit':
                    $order .= 'lastvisit asc';
                    break;
                case 'willing':
                    $order .= 'mib_location_willing desc';
                    break;
                default:
            }
        }

        $query = " SELECT loc.*,
            count(mib_bottles_id) as visits, sum(mib_bottles_quantity) as bottles,max(mib_bottles_date) as lastvisit,
            mib_type_name
            FROM #mib_locations as loc
            LEFT JOIN #mib_type on mib_type_id=mib_location_type_fk
            left outer join #mib_bottles on mib_bottles_location_fk=mib_locations_id
            $where
            group by mib_locations_id 
            $order";
        $numrecs = $this->sql->gen($query, false);

        if ($numrecs > 0)
        {
            $delimiter = ",";
            $filename = "mib_" . date('Y-m-d') . ".csv"; //create a file pointer
            $f = fopen('php://memory', 'w'); //set column headers
            $fields = array(
                'ID',
                'Name',
                'Type',
                'Address 1',
                'Address 2',
                'Town',
                'County',
                "Postcode",
                'Phone',
                'Contact 1',
                'Contact 2',
                'Willing',
                'Comments',
                'Total Visits',
                'Total Bottles',
                'Last Visit');
            fputcsv($f, $fields, $delimiter); //output each row of the data, format line as csv and write to file pointer
            while ($row = $this->sql->fetch())
            {
                $willing = ($row['mib_location_willing'] == '1') ? 'Yes' : 'No';
                $lineData = array(
                    $row['mib_locations_id'],
                    $row['mib_location_name'],
                    $row['mib_type_name'],
                    $row['mib_location_address1'],
                    $row['mib_location_address2'],
                    $row['mib_location_town'],
                    $row['mib_location_county'],
                    $row['mib_location_postcode'],
                    $row['mib_location_phone'],
                    $row['mib_location_contact1'],
                    $row['mib_location_contact2'],
                    $willing,
                    $row['mib_location_comments'],
                    $row['visits'],
                    $row['bottles'],
                    date('Y-m-d', $row['lastvisit']),
                    );
                fputcsv($f, $lineData, $delimiter);
            }

            //move back to beginning of file
            fseek($f, 0); //set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";'); //output all remaining data on a file pointer
            header("Cache-Control: no-cache, no-store, must-revalidate");
            # Disable caching - HTTP 1.0
            header("Pragma: no-cache");
            # Disable caching - Proxies
            fpassthru($f);
        }
    }
    private function ajaxHistRec()
    {
        $qry = "SELECT * FROM #mib_bottles 
            LEFT JOIN #mib_action on mib_action_id=mib_bottles_action_fk
            WHERE mib_bottles_id=" . $this->ajaxid . "
        ";
        $result = $this->sql->gen($qry, false);
        if ($result)
        {
            while ($row = $this->sql->fetch())
            {
                //  print_a($this->sc);
                $this->sc->history = $row; //
                $text .= $this->tp->parseTemplate($this->template->mibAjaxHistoryDetail(), false, $this->sc);
            }

        } else
        {
            $text .= $this->tp->parseTemplate($this->template->mibHistoryNoDetail(), false, $this->sc);
        }
        return $text;
    }
    private function listAll()
    {
        $text .= $this->mibSelector();
        $text .= $this->mibDetail();
        $text .= $this->mibFooter();
        $this->ns->tablerender("Message in a Bottle", $text);
        return $text;
    }
    /**
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */
    private function mibSelector()
    {


        switch ($this->field)
        {
            case 'name':
                $this->sc->order = array(
                    'sortName' => $this->sortDirection,
                    'sortType' => 'none',
                    'sortTown' => 'none',
                    'sortWilling' => 'none',
                    'sortBottle' => 'none');
                if ($this->sortDirection == 'none')
                {
                    $this->qryOrder = '';
                } else
                {
                    $this->qryOrder = " order by mib_location_name " . $this->sortDirection . " ";
                }
                break;
            case 'type':
                $this->sc->order = array(
                    'sortName' => 'none',
                    'sortType' => $this->sortDirection,
                    'sortTown' => 'none',
                    'sortWilling' => 'none',
                    'sortBottle' => 'none');
                if ($this->sortDirection == 'none')
                {
                    $this->qryOrder = '';
                } else
                {
                    $this->qryOrder = " order by mib_type_name " . $this->sortDirection . " ";
                }
                break;
            case 'town':
                $this->sc->order = array(
                    'sortName' => 'none',
                    'sortType' => 'none',
                    'sortTown' => $this->sortDirection,
                    'sortWilling' => 'none',
                    'sortBottle' => 'none');
                if ($this->sortDirection == 'none')
                {
                    $this->qryOrder = '';
                } else
                {
                    $this->qryOrder = " order by mib_location_town " . $this->sortDirection . " ";
                }
                break;
            case 'willing':
                $this->sc->order = array(
                    'sortName' => 'none',
                    'sortType' => 'none',
                    'sortTown' => 'none',
                    'sortWilling' => $this->sortDirection,
                    'sortBottle' => 'none');
                if ($this->sortDirection == 'none')
                {
                    $this->qryOrder = '';
                } else
                {
                    $this->qryOrder = " order by mib_location_willing " . $this->sortDirection . " ";
                }
                break;
            case 'bottle':
                $this->sc->order = array(
                    'sortName' => 'none',
                    'sortType' => 'none',
                    'sortTown' => 'none',
                    'sortWilling' => 'none',
                    'sortBottle' => $this->sortDirection);
                if ($this->sortDirection == 'none')
                {
                    $this->qryOrder = '';
                } else
                {
                    $this->qryOrder = " order by bottles " . $this->sortDirection . " ";
                }
                break;
            default:
                $this->sc->order = array(
                    'sortName' => 'none',
                    'sortType' => 'none',
                    'sortTown' => 'none',
                    'sortWilling' => 'none',
                    'sortBottle' => 'none');
                $this->qryOrder = '';
        }
        //    print_a($this->sc);
        return $this->tp->parseTemplate($this->template->mibSelector(), false, $this->sc);
    }


    private function mibDetail()
    {


        $where = ' WHERE  mib_locations_id > 0 ';
        if (!empty($this->mibSearchName))
        {
            $where .= " AND mib_location_name like '%$this->mibSearchName%' ";
            $this->from = 0;
        }
        if ((int)$this->mibSearchType > 0)
        {
            $where .= " AND mib_location_type_fk=$this->mibSearchType ";
            $this->from = 0;
        }
        if (!empty($this->mibSearchTown))
        {
            $where .= " AND mib_location_town like '%$this->mibSearchTown%' ";
            $this->from = 0;
        }
        //              $this->sc->mibSearchType = $this->mibSearchType;
        //      $this->sc->mibSearchTown = $this->mibSearchTown;
        //      $this->sc->mibSearchName = $this->mibSearchName;
        $this->sc->where = $where;
        $qry = "SELECT mib_locations_id,mib_location_name,mib_location_town,mib_location_willing,mib_type_name,mib_bottles_id, 
            sum(mib_bottles_quantity) as bottles FROM #mib_locations 
            LEFT JOIN #mib_type on mib_type_id=mib_location_type_fk
            left outer join #mib_bottles on mib_bottles_location_fk=mib_locations_id
            $where
            group by mib_locations_id 
            $this->qryOrder
            limit " . $this->from . "," . $this->prefs['perpage'] . "
        ";
        $result = $this->sql->gen($qry, false);
        if ($result)
        {
            while ($row = $this->sql->fetch())
            {
                //  print_a($this->sc);
                $this->sc->data = $row; //
                $text .= $this->tp->parseTemplate($this->template->mibDetail(), false, $this->sc);
            }

        } else
        {
            $text .= $this->tp->parseTemplate($this->template->mibNoDetail(), false, $this->sc);
        }
        return $text;
    }
    private function mibFooter()
    {

        return $this->tp->parseTemplate($this->template->mibFooter(), false, $this->sc);
    }
    private function mibView()
    {
        $text .= $this->mibViewHeader();
        $text .= $this->mibViewDetail();
        $text .= $this->mibViewFooter();
        $text .= $this->mibHistoryHeader();
        $text .= $this->mibHistoryRow();
        $text .= $this->mibHistoryFooter();
        $this->ns->tablerender("Message in a Bottle", $text);
        return $text;
    }
    /**
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */
    private function mibViewHeader()
    {
        $text = $this->tp->parseTemplate($this->template->mibViewHeader(), false, $this->sc);
        return $text;
    }
    private function mibViewDetail()
    {
        $qry = "SELECT *,max(mib_bottles_date) as lastDate,min(mib_bottles_date) as firstDate, 
            sum(mib_bottles_quantity) as bottles FROM #mib_locations 
            LEFT JOIN #mib_type on mib_type_id=mib_location_type_fk
            left outer join #mib_bottles on mib_bottles_location_fk=mib_locations_id
            WHERE mib_locations_id=" . $this->id . "
            group by mib_locations_id
        ";
        $result = $this->sql->gen($qry, false);
        if ($result)
        {
            $row = $this->sql->fetch();
            $this->sc->data = $row;
            $text .= $this->tp->parseTemplate($this->template->mibViewEntry(), false, $this->sc);
        } else
        {
            $text .= $this->tp->parseTemplate($this->template->mibViewNoEntry(), false, $this->sc);
        }
        return $text;
    }
    private function mibViewFooter()
    {
        $text .= $this->tp->parseTemplate($this->template->mibViewFooter(), false, $this->sc);
        return $text;
    }
    private function mibViewHistory()
    {

    }

    private function mibHistoryHeader()
    {

        $text .= $this->tp->parseTemplate($this->template->mibHistoryHeader(), false, $this->sc);
        return $text;
    }
    private function mibHistoryRow()
    {
        $qry = "SELECT *FROM #mib_bottles 
            LEFT JOIN #mib_action on mib_action_id=mib_bottles_action_fk
            WHERE mib_bottles_location_fk=" . $this->id . "
        ";
        $result = $this->sql->gen($qry, false);
        if ($result)
        {
            while ($row = $this->sql->fetch())
            {
                //  print_a($this->sc);
                $this->sc->history = $row; //
                $text .= $this->tp->parseTemplate($this->template->mibHistoryDetail(), false, $this->sc);
            }

        } else
        {
            $text .= $this->tp->parseTemplate($this->template->mibHistoryNoDetail(), false, $this->sc);
        }
        return $text;
    }
    private function mibHistoryFooter()
    {
        $text .= $this->tp->parseTemplate($this->template->mibHistoryFooter(), false, $this->sc);
        return $text;
    }
}
