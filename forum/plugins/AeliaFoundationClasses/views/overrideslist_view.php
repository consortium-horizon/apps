<?php if (!defined('APPLICATION')) exit();


function ShowOverrideStatus($Class) {
	$OverrideInfo = AeliaFoundationClasses::GetOverrideInfo($Class);

	$ClassLabel = Wrap($Class, 'label');
	$Version = GetValue('Version', $OverrideInfo);
	$File = GetValue('File', $OverrideInfo);

	if(!empty($Version)) {
		$Status = T('Loaded');
		$RowCssClass = 'OverrideFound';
	}
	else {
		$Status = T('Not found');
		$RowCssClass = 'OverrideNotFound';
	}

	$Result = '<tr class="' . $RowCssClass . '">';
	$Result .= Wrap($ClassLabel, 'td');
	$Result .= Wrap(T('Loaded'),
									'td',
									array('class' => 'Status'));
	$Result .= Wrap($Version, 'td');
	$Result .= Wrap($File, 'td');
	$Result .= '</tr>';

	echo $Result;
}

?>
<div id="Status">
	<?php
		echo Wrap(T('Status'), 'h3');
		echo Wrap(T('Core Overrides'), 'h4');
		echo Wrap(T('Core overrides are special files that extend some parts of Vanilla Core. ' .
								'The overrides listed below are required for this plugin to work correctly. ' .
								'If you see any message in red, it means that the indicated override has not ' .
								'been loaded, and the plugin might not work correctly. If that is the case, ' .
								'please <a href="http://dev.pathtoenlightenment.net/contact/">contact Support</a>.'),
							'div',
							array('class' => 'Info'));
	?>
	<div class="Overrides">
		<table>
			<thead>
				<?php
					echo Wrap(T('Class'), 'td');
					echo Wrap(T('Loaded file'), 'td');
				?>
			</thead>
			<tbody>
				<?php
					// Retrieve the list of classes for which to show the override status
					$Overrides = $this->Data['Overrides'];
					foreach($Overrides as $Class => $ClassInfo) {
						echo '<tr class="OverrideFound">';
						echo Wrap($Class,
											'td',
											array('class' => 'Label'));
						echo Wrap(GetValue('File', $ClassInfo),
											'td',
											array('class' => 'File'));
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div>
