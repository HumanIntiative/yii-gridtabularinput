<?php

class GridTabularInputWidget extends CWidget
{
	public $title;
	/**
	 * Control per Row. [type, name, title, data, required, htmlOptions, preContent, postContent]
	 * @var array
	 */
	public $controls = array();
	public $data = array();
	public $idField = 'id';
	public $parentIdField = 'parent_id';
	public $idListName = 'taskList';
	public $modelClassName = 'Task';
	public $requiredClassName = 'required';
	public $rowClassName = 'taskRowClone';
	public $addRowClassName = 'addTaskRow';
	public $delRowClassName = 'delTaskRow';
	public $btnSaveClassName = 'btnSave';
	public $scrIdName = 'gridTabXYZ123';
	public $formName = 'frmForm';
	public $titleClassName = 'titleName';
	public $calendarClassName = 'calendarTask';
	public $messageOnErrorSubmitForm = 'Silakan lengkapi isian Task/Activity!';
	public $withHeader = true;
	public $cloneWithEvent = 'false';

	public function init()
	{
		parent::init();

		$cs = Yii::app()->getClientScript();
		$script = "var idListName  	= idListName || '#{$this->idListName}';\r";
		$script .= "var addTaskRow  = addTaskRow || '.{$this->addRowClassName}';\r";
		$script .= "var delTaskRow  = delTaskRow || '.{$this->delRowClassName}';\r";
		$script .= "var btnSave 	= btnSave 	 || '#{$this->btnSaveClassName}';\r";
		$script .= "var taskRowClone = taskRowClone || '{$this->rowClassName}';\r";
		$script .= "var titleName 	= titleName || '.{$this->titleClassName}';\r";
		$script .= "var formName 	= formName   || '#{$this->formName}';\r";
		$script .= "var calendarTask 	= calendarTask   || '{$this->calendarClassName}';\r";
		$script .= "var messageSubmit = messageSubmit || '{$this->messageOnErrorSubmitForm}';\r";
		$script .= "var withDataAndEvents = withDataAndEvents || {$this->cloneWithEvent};\r";
		$script .= file_get_contents(dirname(__FILE__).'/script.js');

		$cs->registerPackage('datepicker');
		$cs->registerScript($this->scrIdName, $script, CClientScript::POS_READY);
	}

	public function run()
	{
		if ($this->withHeader): ?>
		<div class="panel panel-primary filterable">
			<div class="panel-heading">
				<h3 class="panel-title"><?=$this->title?></h3>
			</div>
		<?php endif; ?>
			<table class="table">
				<thead>
					<tr class="filters">
						<th style="width:75px;">No</th>
						<?php foreach ($this->controls as $ctrl): ?>
						<?php if($ctrl['title']): ?><th><?=$ctrl['title']?></th><?php endif; ?>
						<?php endforeach; ?>
						<th class="col-sm-2">Actions</th>
					</tr>
				</thead>
				<tbody id="<?=$this->idListName?>">
					<?php $num = 1; $class = 'odd'; ?>
					<?php foreach ($this->data as $task): ?>
						<?php $this->rowTemplate($num++, $class, $task); ?>
					<?php endforeach ?>
					<?php $this->rowTemplate($num, $class, array()); ?>
				</tbody>
			</table>
		<?php if ($this->withHeader): ?>
		</div>
		<?php endif; ?>

		<script type="text/javascript">
			$(function(){
				$('.<?=$this->calendarClassName?>').datepicker({
					'language': 'id',
					'format': 'yyyy-mm-dd',
					'viewformat': 'yyyy-mm-dd',
					'placement': 'right',
					'autoclose': 'true'
				});
			});
		</script>
		<?php
	}

	protected function rowTemplate($index, $classOddEven, $paramData)
	{
		$disabled = ($index==1) ? array('disabled'=>true) : array(); ?>
		<tr class="<?=$this->rowClassName?> <?=($classOddEven=='odd')?'even':'odd'?>">
			<td>
				<div class="num"><?=$index?></div>
				<?php echo TbHtml::hiddenField("{$this->modelClassName}[{$this->idField}][]", $paramData[$this->idField]); ?>
				<?php echo TbHtml::hiddenField("{$this->modelClassName}[{$this->parentIdField}][]", $paramData[$this->parentIdField]); ?>
				<?php foreach ($this->controls as $control): ?>
					<?php if ($control['type'] == 'hiddenField'): ?>
					<?php $this->controlTemplate($control, $paramData); ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</td>
			<?php foreach ($this->controls as $control): ?>
				<?php if ($control['type'] != 'hiddenField'): ?>
				<td><?php $this->controlTemplate($control, $paramData); ?></td>
				<?php endif; ?>
			<?php endforeach; ?>
			<td><?php $this->widget('booster.widgets.TbButtonGroup', [
				'buttons' => array(
					array('icon'=>'plus', 'url' => '#', 'htmlOptions'=>['class'=>$this->addRowClassName]),
					array_merge(
						array('icon'=>'remove', 'url' => '#', 'htmlOptions'=>['class'=>$this->delRowClassName]),
						$disabled
					),
				),
			]); ?></td>
		</tr>
		<?php
	}

	public function controlTemplate($control, $paramData)
	{
		$name        = "{$this->modelClassName}[{$control[name]}][]";
		$value       = $paramData[$control[name]];
		$class       = ($control['type'] == 'dateField') ? 'form-control '.$this->calendarClassName : 'form-control';
		$class       = ($control['required']) ? "{$class} {$this->requiredClassName} " : "{$class}";
		$class 		 = ($control['htmlOptions'] && ($cls = $control['htmlOptions']['class'])) ? "$class $cls" : $class;
		$options     = array($name, $value);
		$htmlOptions = CMap::mergeArray($control['htmlOptions'], array('class'=>$class, 'placeholder'=>$control['title']));

		if ($control['type'] == 'dropDownList')
			array_push($options, $control['data']);
		array_push($options, $htmlOptions); ?>

		<?php if ($control['type'] == 'dateField'): ?>
			<div class="input-group">
				<?php echo TbHtml::textField($name, $value, $htmlOptions); ?>
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			</div>
		<?php else: ?>
			<?=$control['preContent']?>
			<?=call_user_func_array(array('TbHtml', $control['type']), $options)?>
			<?=$control['postContent']?>
		<?php endif; ?>
		<?php
	}
}