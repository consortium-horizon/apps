<?php if(!defined('APPLICATION')) exit();
/**
 * This rule awards badges if a discussion body contains a given string
 *
 * @author Aaron Webstey
 * @since 1.0
 * @package Yaga
 */
 
class DiscussionWordMention implements YagaRule{
public function Award($Sender, $User, $Criteria) {
$PostInfo = $Sender->EventArguments['FormPostValues'];
$PBodyText = val('Body',$PostInfo);
$PUserID =  val('InsertUserID',$PostInfo);
    if (strpos(strtolower($PBodyText), strtolower($Criteria->WordToMatch)) !== FALSE) {
     return $PUserID;
    }
 return FALSE;
}
public function Form($Form) {
$String = $Form->Label('Yaga.Rules.DiscussionWordMention.Criteria.Head', 'DiscussionWordMention');
$String .= $Form->Textbox('WordToMatch', array('class' => 'WideInput'));
return $String;
}
public function Validate($Criteria, $Form) {
$Validation = new Gdn_Validation();
$Validation->ApplyRules(array(
array(
'Name' => 'WordToMatch', 'Validation' => array('Required')
)
));
$Validation->Validate($Criteria);
$Form->SetValidationResults($Validation->Results());
}
public function Hooks() {
return array('CommentModel_AfterSaveComment', 'DiscussionModel_AfterSaveDiscussion');
}
public function Description() {
$Description = sprintf(T('Yaga.Rules.DiscussionWordMention.Desc'), C('Vanilla.Comment.MaxLength'));
return Wrap($Description, 'div', array('class' => 'InfoMessage'));
}
public function Name() {
return T('Yaga.Rules.DiscussionWordMention');
}
public function Interacts() {
return FALSE;
}
}
