<?php

class mibTemplate
{
    private $roles;
    public $from;
    public $rolesValue;
    function __construct()
    {
        $sql = e107::getDb();
        $sql->select('mib_roles', 'mib_roles_id,mib_roles_role', '', 'nowhere');
        $this->types[0] = 'All Types';
        $this->frm = e107::getForm();
        while ($row = $sql->fetch('assoc'))
        {

            $this->types[$row['mib_roles_id']] = $row['mib_roles_role'];
        }
    }
    function notPermitted()
    {
        $retval .= '
<div id="mibHeading">
    <a href="../../index.php">
        <i class="fa fa-home fa-3" aria-hidden="true"></i>
    </a>
</div>
<div id="mibNoPermit">' . LAN_PLUGIN_MIB_FRONT_NOTPERMITTED . '</div>';
        return $retval;
    }
    function noRecords()
    {
        $retval .= '
<div id="mibHeading">
    <a href="../../index.php">
        <i class="fa fa-home fa-3" aria-hidden="true"></i>
    </a>
</div>
<div id="mibNoRecords">".LAN_PLUGIN_MIB_FRONT_NOMATCH."</div>';
        return $retval;
    }
    function mibSelector()
    {


        $retval = '
<div class="mibHead">';
        $retval .= $this->frm->open('mibSearch', 'post', e_SELF, null);
        //    $retval .= '
        // <div class="mibSelect">' . $select . '</div>';
        $retval .= $this->frm->hidden('action', 'list');
        $retval .= $this->frm->hidden('id', $this->id);
        $retval .= $this->frm->hidden('from', $this->from);
        $retval .= $this->frm->hidden('historyFrom', $this->historyFrom);
        $retval .= $this->frm->hidden('field', $this->field);

        $retval .= '
           
    <div id="mibPage">
        <table class="addressTable table table-bordered table-striped table-hover table-condensed table-responsive">
            <thead>
                <tr>
                    <th>{MIB_SORT_NAME} {MIB_SEARCH_NAME}</th>
                    <th>{MIB_SORT_TYPE} {MIB_SEARCH_TYPE}</th>
                    <th>{MIB_SORT_TOWN} {MIB_SEARCH_TOWN}</th>                
                    <th style="vertical-align:top" >{MIB_SORT_WILLING}</th>
                    <th style="vertical-align:top" >{MIB_SORT_BOTTLES}</th>
                    <th style="vertical-align:bottom" class="mib_center">{MIB_SEARCH_SUBMIT}</th>
                </tr>
                <tr>
                    <th>
	   			' . LAN_PLUGIN_MIB_FRONT_NAME . '
                    </th>
                    <th>
				' . LAN_PLUGIN_MIB_FRONT_TYPE . '
                    </th>
                    <th>
				' . LAN_PLUGIN_MIB_FRONT_TOWN . '
                    </th>                
                    <th class="mib_center">
				' . LAN_PLUGIN_MIB_FRONT_WILLING . '
                    </th>
                    <th class="mib_right">
				' . LAN_PLUGIN_MIB_FRONT_BOTTLES . '
                    </th>
                    <th class="mib_center">
				' . LAN_PLUGIN_MIB_FRONT_VIEW . '
                    </th>
                </tr>
            </thead>
            <tbody>';


        return $retval;
    }
    function mibDetail()
    {
        $retval = '
                <tr id="mibListRow-{MIB_ID}" class="" >
                    <td>{MIB_NAME}</td>
                    <td>{MIB_TYPE}</td>
                    <td>{MIB_TOWN}</td>
                    <td class="mib_center">{MIB_WILLING}</td>
                    <td class="mib_right">{MIB_BOTTLES}</td>
                    <td class="mib_center">{MIB_HISTORY}</td>
                </tr>';
        return $retval;
    }
    function mibNoDetail()
    {
        $retval = '
                <tr>
                    <td colspan="6">
	       			    <div id="mibNone" >' . LAN_PLUGIN_MIB_FRONT_NOMATCH . '</div>
                    </td>
                </tr>';
        return $retval;
    }

    function mibFooter($nextPrev = '')
    {
        $retval .= '  
            </tbody>
        </table>';
        $retval .= $this->frm->close();
        $retval .= ' 
    </div>
</div>

<div style="font-size:32px;float:left;display:inline;">{MIB_NEXTPREV}</div>
<div style="font-size:32px;float:right;display:inline;">
    <!--<a href="' . e_PLUGIN_ABS . 'mib/index.php?action=pdflist&from=' . $this->id . '" id="mibpdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>-->
    <a href="' . e_PLUGIN_ABS . 'mib/index.php?action=csvlist&search=' . $this->search . '&role=' . $this->rolesValue . '"><i class="fa fa-download" aria-hidden="true"></i></a>
    <!--<a href="' . e_PLUGIN_ABS . 'mib/index.php?action=prn"><i class="fa fa-print" aria-hidden="true"></i></a>-->
</div>
<a href="' . e_PLUGIN_ABS . 'mib/index.php?action=ajaxview&id=" id="modallink" data-remote="false" data-toggle="modal" data-target="#myModal" class="btnx btnx-default"></a>

';

        return $retval;
    }
    function mibViewHeader()
    {
        $retval = '
        {MIB_VIEW_BACK}
        ';
        return $retval;
    }
    function mibViewNoEntry()
    {
        $retval = '
        <table id="mibViewRecord" >
            <tbody>
                <tr  >
                    <td >' . LAN_PLUGIN_MIB_FRONT_NOMATCH . '</td>
                </tr>
        	</tbody>
        </table>';
        return $retval;
    }
    function mibViewEntry()
    {
        $retval = '
        <table id="mibViewRecord"  class="addressTable table table-bordered table-condensed table-responsive" >
            <tbody>
                <tr  >
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_NAME . '</td>
                    <td class="contentCol addressCell" >{MIB_NAME}</td>
                    <td class="titleCol commsCell" >' . LAN_PLUGIN_MIB_FRONT_TYPE . '</td>
                    <td class="contentCol commsCell" >{MIB_TYPE}</td>
                </tr>
                <tr  >
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_ADDRESS . '</td>
                    <td class="contentCol addressCell" >{MIB_ADDRESS1}</td>
                    <td class="titleCol commsCell" >' . LAN_PLUGIN_MIB_FRONT_WILLING . '</td>
                    <td class="contentCol commsCell" >{MIB_WILLING}</td>
                </tr>
                <tr  >
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_ADDRESS . '</td>
                    <td class="contentCol addressCell" >{MIB_ADDRESS2}</td>
                    <td class="titleCol commsCell" >' . LAN_PLUGIN_MIB_FRONT_CONTACT . '</td>
                    <td class="contentCol commsCell" >{MIB_CONTACT1}</td>
                </tr>
                <tr  >
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_TOWN . '</td>
                    <td class="contentCol addressCell" >{MIB_TOWN}</td>
                    <td class="titleCol commsCell" >' . LAN_PLUGIN_MIB_FRONT_CONTACT . '</td>
                    <td class="contentCol commsCell" >{MIB_CONTACT12}</td>
                </tr>
                <tr  >
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_COUNTY . '</td>
                    <td class="contentCol addressCell" >{MIB_COUNTY}</td>
                    <td class="titleCol commsCell" >' . LAN_PLUGIN_MIB_FRONT_FIRST . '</td>
                    <td class="contentCol commsCell" >{MIB_FIRST_ACTIVITY}</td>
                </tr>
                <tr >
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_POST . '</td>
                    <td class="contentCol addressCell" >{MIB_POSTCODE}</td>
                    <td class="titleCol addressCell" >' . LAN_PLUGIN_MIB_FRONT_LAST . '</td>
                    <td class="contentCol addressCell" >{MIB_LAST_ACTIVITY}</td>
                </tr>     
                <tr >
                    <td class="titleCol notesCell" >' . LAN_PLUGIN_MIB_FRONT_NOTES . '</td>
			         <td  class="contentCol notesCell"  colspan="3">{MIB_COMMENTS}</td>
                </tr>
        	</tbody>
        </table>
';
        return $retval;
    }
    function mibViewFooter()
    {
    }
    function mibHistoryHeader()
    {
        //247 6933
        return "
<div id='mibHistoryContainer' >
    <table id='mibHistoryTable'  class='addressTable table table-bordered table-striped table-hover table-condensed table-responsive'>
        <tr>
            <th>Date</th>
            <th>Action</th>
            <th>Bottles</th>
            <th>Lion</th>
            <th>Comment</th>
            <th>&nbsp;</th>
        </tr>
        ";
    }
    function mibHistoryDetail()
    {
        return "
        <tr >
            <td class='mibHistDate' >{MIB_HISTORY_DATE}</td>
            <td class='mibHistAction' >{MIB_HISTORY_ACTION}</td>
            <td class='mibHistBottle' >{MIB_HISTORY_BOTTLES}</td>
            <td class='mibHistLion' >{MIB_HISTORY_LION}</td>
            <td class='mibHistComment' >{MIB_HISTORY_COMMENT}</td>
            <td class='mibHistViewRec' >{MIB_HISTORY_VIEWREC}</td>
        </tr>";

    }
    function mibAjaxHistoryDetail()
    {
        return "
        <table  class='addressTable table table-bordered table-condensed table-responsive' >
        <tr >
            <td class='mibcol13Title mibCol13' >Date</td>
            <td class='mibCol24Title mibCol24' >{MIB_HISTORY_ADATE}</td>
            <td class='mibcol13Title mibCol13' >Action</td>
            <td class='mibCol24Title mibCol24'>{MIB_HISTORY_ACTION}</td>
        </tr>
        <tr>
            <td class='mibcol13Title mibCol13' >Bottles</td>
            <td class='mibCol24Title mibCol24' >{MIB_HISTORY_BOTTLES}</td>
            <td class='mibcol13Title mibCol13' >Lion</td>
            <td class='mibCol24Title mibCol24' >{MIB_HISTORY_LION}</td>
        </tr>
        <tr>
            <td class='mibcol13Title mibCol13'  >Comments</td>
            <td class='' colspan='3' >{MIB_HISTORY_COMMENT}</td>

        </tr>
        </table>";

    }

    function mibHistoryNoDetail()
    {
        return " <tr>
            <td class='mibHistDate' >no history of activity</td>
        </tr>";

    }
    function mibHistoryFooter()
    {
        return '
    </table>
</div>
<!-- Default bootstrap modal example -->

<div class="modal fade" id="myMibModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">' . LAN_PLUGIN_MIB_FRONT_ENTRY . '</h4>
      </div>
      <div id="modal-body" class="modal-body">
            <div id="ajaxSpinner" class="lds-css ng-scope">
                <img src="images/loader.gif" /><div id="ajaxSpinnerSpin">
                    <div id="loadingAB">' . LAN_PLUGIN_MIB_FRONT_LOADING . '</div>
                </div>
            </div> 
            <div id="modalContent"></div> 
      </div> <!-- end of modal body -->
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">' . LAN_PLUGIN_MIB_FRONT_CLOSE . '</button>
        </div>
    </div>
  </div>
</div>';
    }


    function showEntry($row)
    {
        $retval = '
<div id="mibHeading">
    <a href="index.php?action=list&from=' . $this->from . '&search=' . $this->search . '&role=' . $this->rolesValue . '">
        <i class="fa fa-home fa-3" aria-hidden="true"></i>
    </a>
</div>';
        $retval .= $this->viewEntry($row);
        /*
        * <div id="mibPage">
        * <table  >
        * <tbody>
        * <tr  >
        * <td class="titleCol  addressCell" >Name</td>
        * <td class="contentCol addressCell" >'.$row['mib_lastname'].', '.$row['mib_firstname'].'</td>
        * <td class="titleCol commsCell" >Phone</td>
        * <td class="contentCol commsCell" >'.$row['mib_phone'].'</td>
        * </tr>
        * <tr  >
        * <td class="titleCol addressCell" >Address</td>
        * <td class="contentCol addressCell" >'.$row['mib_addr1'].'</td>
        * <td class="titleCol commsCell" >Mobile</td>
        * <td class="contentCol commsCell" >'.$row['mib_mobile'].'</td>
        * </tr>
        * <tr  >
        * <td class="titleCol addressCell" >Address</td>
        * <td class="contentCol addressCell" >'.$row['mib_addr2'].'</td>
        * <td class="titleCol commsCell" >Email</td>
        * <td class="contentCol commsCell" >'.$row['mib_email1'].'</td>
        * </tr>
        * <tr  >
        * <td class="titleCol addressCell" >Town</td>
        * <td class="contentCol addressCell" >'.$row['mib_city'].'</td>
        * <td class="titleCol commsCell" >Email (alt)</td>
        * <td class="contentCol commsCell" >'.$row['mib_email2'].'</td>
        * </tr>
        * <tr  >
        * <td class="titleCol addressCell" >County</td>
        * <td class="contentCol addressCell" >'.$row['mib_county'].'</td>
        * <td class="titleCol commsCell" >Web</td>
        * <td class="contentCol commsCell" >'.$row['mib_website'].'</td>
        * </tr>
        * <tr style="height: 20px;">
        * <td class="titleCol addressCell" >Postcode</td>
        * <td class="contentCol addressCell" >'.$row['mib_postcode'].'</td>
        * <td class="titleCol commsCell" >&nbsp;</td>
        * <td class="contentCol commsCell" >&nbsp;</td>
        * </tr>
        * <tr style="height: 20px;">
        * <td class="titleCol roleCell" >Role</td>
        * <td class="contentCol roleCell" >'.$row['mib_roles_role'].'</td>
        * <td class="titleCol roleCell" >Category</td>
        * <td class="contentCol roleCell" >'.$row['mib_categories_name'].'</td>
        * </tr>        
        * <tr style="height: 20px;">
        * <td class="titleCol notesCell" >Notes</td>
        * <td  class="contentCol notesCell"  colspan="3">'.$row['mib_comments'].'</td>
        * </tr>

        * </tbody>
        * </table>
        * </div>
        * <!-- DivTable.com -->
        * ';
        */
        return $retval;
    }
    function getcsv(){
    
        $retval .= '
<div class="mibHead">';
        $retval .= $this->frm->open('mibSearch', 'post', e_SELF, null);

        $retval .= $this->frm->hidden('action', 'genCsv');
        $retval .= $this->frm->hidden('id', $this->id);
        $retval .= $this->frm->hidden('from', $this->from);
        $retval .= $this->frm->hidden('historyFrom', $this->historyFrom);
        $retval .= $this->frm->hidden('field', $this->field);

        $retval .= '
           
    <div id="mibPage">
        <table class="addressTable table table-bordered  table-condensed table-responsive">
            <tbody>
                <tr>
                    <td>{MIB_VIEW_BACK}</td>
                </tr>
                <tr>
                    <td>Filter</td>
                </tr>
                <tr>
                    <td>{MIB_SEARCH_TYPE}</td>
                </tr>
                <tr>
                    <td>{MIB_SEARCH_TOWN}</td>                
                </tr>
                <tr>
                    <td>Sort by</td>
                </tr>      
                <tr>
                    <td>
                        Sort 1 :<br>{MIB_SORTLIST1NAME}
                    </td>
                </tr> 
                <tr>                    
                    <td>
                        Sort 2 :<br>{MIB_SORTLIST2NAME}
                    </td>
                </tr>     
                <tr>
                    <td style="vertical-align:bottom" class="mib_center">{MIB_CSV_SUBMIT}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    ';
    $retval .= $this->frm->close();
    return $retval;
        }
}
