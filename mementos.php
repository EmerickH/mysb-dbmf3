<?php 
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2012
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if(!MySBRoleHelper::checkAccess('dbmf_user')) return;

echo '
<h1>'._G('DBMF_mementos_summary').'</h1>

<div id="mysbMenuLevel">
    <a href="index.php?mod=dbmf3&amp;tpl=mementos">'._G('DBMF_mementos_actives').'</a>
    <a href="index.php?mod=dbmf3&amp;tpl=mementos&amp;filter=all">'._G('DBMF_mementos_all').'</a>
</div>
';

if($_GET['filter']=='all') {
    $mementos_p = MySBDBMFMementoHelper::load();
    echo '
<h2>'._G('DBMF_mementos_all').' ('.count($mementos_p).')</h2>';
} else {
    $mementos_p = MySBDBMFMementoHelper::loadActives();
    echo '
<h2>'._G('DBMF_mementos_actives').' ('.count($mementos_p).')</h2>';
}


$memento_type = -1;

foreach($mementos_p as $memento) {
    if($memento->type!=$memento_type) {
        if($memento_type!=-1) echo '
</tbody></table>
</div>';
    $memento_type = $memento->type;
    if($memento_type==0) $h3 = 'DBMF_memento_type_punctual';
    elseif($memento_type==1) $h3 = 'DBMF_memento_type_monthofyear';
    echo '
<div class="table_support">
<table style="font-size: 85%; width: 95%;"><tbody>
<tr class="title" style="text-align: center;">
    <td colspan="7" style="font-size: 120%;">'._G($h3).'</td>
</tr>
<tr class="title">
    <td style="width: 120px;">'._G("DBMF_memento_date").'</td>
    <td style="width: 28px;"></td>
    <td style="width: 200px;">'._G('DBMF_mementos_details').'</td>
    <td>'._G('DBMF_memento_comments').'</td>
    <td style="width: 160px;">'._G('DBMF_memento_comments2').'</td>
    <td style="width: 90px;"></td>
    <td style="width: 90px;"></td>
</tr>';
    }
    $contact = new MySBDBMFContact($memento->contact_id);
    //$memento_date = new MySBDateTime($memento->date_memento);
    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->group_id!=0) $m_group = MySBGroupHelper::getByID($memento->group_id);
    else $m_group = null;
    if($memento->isActive()) $Active = true;
    else $Active = false;
    if($Active) $memclass = 'class="mem_active"';
    elseif(!$Active and $memento->date_process!='') $memclass = 'class="mem_processed"';
    else $memclass='';
    echo '
<tr '.$memclass.'>
    <td>';
    if($memento->isEditable()) echo '
        <a  name="contact'.$anchor_nb.'"
            href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editmemento&amp;memento_id='.$memento->id.'\',\'contactinfos\')"><b>'.$memento->getDate().'</b></a>';
    else echo '
        '.$memento->getDate().'';
    echo '
    </td>
    <td>
        <a  name="contact'.$anchor_nb.'"
            href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'\',\'contactinfos\')">
        <img src="modules/dbmf3/images/edit_icon24.png" alt="Edition '.$contact->id.'" title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' (memento '.$memento->id.')">
        </a>
    </td>
    <td>
        <b>'.$contact->lastname.'</b> '.$contact->firstname.'<br>
        <small><i>'.$m_user->login.'('.$m_group->name.')</i></small>
    </td>
    <td>
        '.$memento->comments.'
    </td>
    <td>
        '.$memento->comments2.'
    </td>
    <td style="text-align: right;">';
    if($memento->date_process!='') {
        $memento_process = new MySBDateTime($memento->date_process);
        echo '
        <small>'._G('DBMF_memento_process_last').':<br>'.$memento_process->strEBY_l().'</small>';
    }
    echo '
    </td>
    <td style="text-align: center;">';
    if($Active) {
        echo '
        <form action="index.php?mod=dbmf3&amp;tpl=mementos&amp;filter='.$_GET['filter'].'" method="post">
            <input type="hidden" name="memento_process" value="'.$memento->id.'">
            <input type="submit" value="'._G('DBMF_memento_process_submit').'">
        </form>';
    } elseif(!$Active and $memento->date_process!='') {
        echo '
        <form action="index.php?mod=dbmf3&amp;tpl=mementos&amp;filter='.$_GET['filter'].'" method="post">
            <input type="hidden" name="memento_unprocess" value="'.$memento->id.'">
            <input type="submit" value="'._G('DBMF_memento_unprocess_submit').'">
        </form>';
    }
    echo '
    </td>
</tr>';
}

echo '
</tbody></table>
</div>';



?>
