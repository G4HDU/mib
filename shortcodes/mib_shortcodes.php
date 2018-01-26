<?php

class mib_shortcodes extends e_shortcode
{
    public $data;
    public function __construct()
    {
        $this->prefs = e107::pref('mib');
    }
    function sc_mib_view_back()
    {

        $retval = '<span style="font-size:32px"><a href="' . e_PLUGIN . 'mib/index.php?action=list&from=' . $this->from . '&historyFrom=' . $this->historyFrom . '&id=' . $this->data['mib_locations_id'] .
            '" ><i class="fa fa-arrow-left" aria-hidden="true"></i></a></span>';
        return $retval;

    }
    function sc_mib_sort_name()
    {
        switch ($this->order['sortName'])
        {
            case 'asc':
                $retval = '<button type="submit" name="mibNameSort" value="desc"><i class="fa fa-sort-asc" aria-hidden="true"></i></button>';
                break;
            case 'desc':
                $retval = '<button type="submit" name="mibNameSort" value="none" ><i class="fa fa-sort-desc" aria-hidden="true"></i></button>';
                break;
            case 'none':
            default:
                $retval = '<button type="submit" name="mibNameSort" value="asc"><i class="fa fa-sort" aria-hidden="true"></i></button>';
                break;
        }

        return $retval;
    }
    function sc_mib_sort_type()
    {

        switch ($this->order['sortType'])
        {
            case 'asc':
                $retval = '<button type="submit" name="mibTypeSort" value="desc"><i class="fa fa-sort-asc" aria-hidden="true"></i></button>';
                break;
            case 'desc':
                $retval = '<button type="submit" name="mibTypeSort" value="none"><i class="fa fa-sort-desc" aria-hidden="true"></i></button>';
                break;
            case 'none':
            default:
                $retval = '<button type="submit" name="mibTypeSort" value="asc"><i class="fa fa-sort" aria-hidden="true"></i></button>';
                break;
        }

        return $retval;
    }
    function sc_mib_sort_town()
    {
        switch ($this->order['sortTown'])
        {
            case 'asc':
                $retval = '<button type="submit" name="mibTownSort" value="desc"><i class="fa fa-sort-asc" aria-hidden="true"></i></button>';
                break;
            case 'desc':
                $retval = '<button type="submit" name="mibTownSort" value="none"><i class="fa fa-sort-desc" aria-hidden="true"></i></button>';
                break;
            case 'none':
            default:
                $retval = '<button type="submit" name="mibTownSort" value="asc"><i class="fa fa-sort" aria-hidden="true"></i></button>';
                break;
        }

        return $retval;
    }
    function sc_mib_sort_willing()
    {
        switch ($this->order['sortWilling'])
        {
            case 'asc':
                $retval = '<button type="submit" name="mibWillingSort" value="desc"><i class="fa fa-sort-asc" aria-hidden="true"></i></button>';
                break;
            case 'desc':
                $retval = '<button type="submit" name="mibWillingSort" value="none"><i class="fa fa-sort-desc" aria-hidden="true"></i></button>';
                break;
            case 'none':
            default:
                $retval = '<button type="submit" name="mibWillingSort" value="asc"><i class="fa fa-sort" aria-hidden="true"></i></button>';
                break;
        }
        return $retval;
    }
    function sc_mib_sort_bottles()
    {
        switch ($this->order['sortBottle'])
        {
            case 'asc':
                $retval = '<button type="submit" name="mibBottleSort" value="desc"><i class="fa fa-sort-asc" aria-hidden="true"></i></button>';
                break;
            case 'desc':
                $retval = '<button type="submit" name="mibBottleSort" value="none"><i class="fa fa-sort-desc" aria-hidden="true"></i></button>';
                break;
            case 'none':
            default:
                $retval = '<button type="submit" name="mibBottleSort" value="asc"><i class="fa fa-sort" aria-hidden="true"></i></button>';
                break;
        }
        return $retval;
    }
    function sc_mib_search_name()
    {
        return e107::getForm()->text('mibSearchName', $this->mibSearchName, 20);
    }

    function sc_mib_search_type()
    {
        $qry = "SELECT mib_type_id,mib_type_name FROM #mib_type ORDER BY mib_type_name";
        $sql = e107::getDB();
        $frm = e107::getForm();
        $result = $sql->gen($qry, false);

        while ($row = $sql->fetch())
        {
            $opts[$row['mib_type_id']] = $row['mib_type_name'];

        }

        $selectType = $frm->select('mibSearchType', $opts, $this->mibSearchType, array(), true);
        return $selectType;
    }
    function sc_mib_search_town()
    {
        $qry = "SELECT DISTINCT mib_location_town FROM #mib_locations ORDER BY mib_location_town";
        $sql = e107::getDB();
        $frm = e107::getForm();
        $result = $sql->gen($qry, false);

        while ($row = $sql->fetch())
        {
            $opts[$row['mib_location_town']] = $row['mib_location_town'];
            // print_a($opts);
        }

        $selectType = $frm->select('mibSearchTown', $opts, $this->mibSearchTown, array(), true);
        return $selectType;
    }
    function sc_mib_search_submit()
    {

        return '<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>';
    }
    function sc_mib_id()
    {
        return $this->data['mib_locations_id'];
    }
    function sc_mib_name()
    {
        return $this->data['mib_location_name'];
    }
    function sc_mib_type()
    {
        return $this->data['mib_type_name'];
    }
    function sc_mib_town()
    {
        return $this->data['mib_location_town'];
    }
    function sc_mib_willing()
    {
        if ($this->data['mib_location_willing'] > 0)
        {
            return '<i class="fa fa-check" aria-hidden="true"></i>';
        } else
        {
            return '<i class="fa fa-times" aria-hidden="true"></i>';
        }
    }
    function sc_mib_bottles()
    {
        return (int)$this->data['bottles'];
    }
    function sc_mib_nextprev()
    {
        $total = e107::getDB()->count('mib_locations', '(mib_locations_id)', $this->where, false);
        $amount = $this->prefs['perpage'];
        $current = $this->from;
        $fields = "&mibSearchType=$this->mibSearchType";
        $fields .= "&mibSearchTown=$this->mibSearchTown";
        $fields .= "&mibSearchName=$this->mibSearchName";
        $fields .= "&sortDirection=$this->sortDirection";
        $fields .= "&sortField=$this->sortField";
        $oldUrl = e_SELF . '?action=list&from=--FROM--&search=' . $fields;
        $url = rawurlencode($oldUrl);
        $type = 'record';
        $parm = "total={$total}&amount={$amount}&current={$current}&type={$type}&url={$url}";
        //print_a($oldUrl);
        $nextprev = e107::getParser()->parseTemplate("{NEXTPREV={$parm}}");

        return $nextprev;
    }
    function sc_mib_history()
    {
        // var_dump($this);
        $retval = '<a href="' . e_PLUGIN . 'mib/index.php?action=view&from=' . $this->from . '&historyFrom=' . $this->historyFrom . '&id=' . $this->data['mib_locations_id'] .
            '" ><i class="fa fa-bullseye" aria-hidden="true"></i></a>';
        return $retval;
    }
    function sc_mib_first_activity()
    {
        return e107::getParser()->toDate($this->data['firstDate'], 'short');
    }
    function sc_mib_last_activity()
    {
        return e107::getParser()->toDate($this->data['lastDate'], 'short');
    }

    function sc_mib_comments()
    {
        return $this->data['mib_location_comments'];
    }
    function sc_mib_history_date()
    {
        return e107::getParser()->toDate($this->data['mib_bottles_date'], 'short');
    }
    function sc_mib_history_adate()
    {
        return e107::getParser()->toDate($this->history['mib_bottles_date'], 'short');
    }
    function sc_mib_history_action()
    {
        return $this->history['mib_action_action'];
    }
    function sc_mib_history_bottles()
    {
        return $this->history['mib_bottles_quantity'];
    }
    function sc_mib_history_lion()
    {
        return $this->history['mib_bottles_user'];
    }
    function sc_mib_history_comment()
    {
        return $this->history['mib_bottles_comments'];
    }
    function sc_mib_history_viewrec()
    {
        // var_dump($this);
        $id = $this->data['mib_locations_id'];
        $retval = '<a class="mibRow editID' . $id . '" href="' . e_PLUGIN . 'mib/index.php?ajax=false&ajaxid=' . $this->history['mib_bottles_id'] . '&action=histrec&from=' . $this->from . '&historyFrom=' .
            $this->historyFrom . '&id=' . $id . '" ><i class="fa fa-bullseye" aria-hidden="true"></i></a>';
        return $retval;

    }
    function sc_mib_sortlist1name()
    {
        $frm = e107::getForm();
        $listButtons = array(
            "name" => 'Name',
            'type' => 'Type',
            'town' => 'Town',
            'bottles' => 'Bottles',
            'visit' => 'Last Visit',
            'willing' => 'Willing');
        $retval .= $frm->radio("mibSort1", $listButtons);
        return $retval;
    }
    function sc_mib_sortlist2name()
    {
        $frm = e107::getForm();
        $listButtons = array(
            "name" => 'Name',
            'type' => 'Type',
            'town' => 'Town',
            'bottles' => 'Bottles',
            'visit' => 'Last Visit',
            'willing' => 'Willing');
        $retval .= $frm->radio("mibSort2", $listButtons);
        return $retval;
    }
    function sc_mib_csv_submit()
    {
        $frm = e107::getForm();
        $retval .= $frm->button("mibCSV", 'Generate', 'submit', '', array('loading' => false));
        return $retval;
    }
}
