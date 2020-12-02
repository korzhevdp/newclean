				<tr ref="<?=$id;?>" class="<?=$inactiveClass?> settingCaptionRow">
					<td ref="<?=$id;?>" class="settingCaption"><?=$statusName;?></td>
					<td class="settingCaptionBg">
						<button class="editItem pull-right btn btn-small" ref="<?=$id;?>"><?=$buttonLabel;?></button>
						<button class="saveItem pull-right btn btn-small hide" ref="<?=$id;?>">Сохранить</button>
					</td>
				</tr>
				<tbody ref="<?=$id;?>" class="settingSection hide<?=$inactiveClass;?>">
					<tr><th colspan=2>Статус</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="statusName" value="<?=$statusName;?>"></td></tr>
					<tr><th colspan=2>Иконка</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="statusIcon" value="<?=$statusIcon;?>"></td></tr>
					<tr><th colspan=2>Цвет стиля</th></tr>
					<tr><td colspan=2><input type="color" ref="<?=$id;?>" class="formField" role="statusColor" value="<?=$styleColor;?>"></td></tr>
					<tr><th colspan=2>Цвет Web</th></tr>
					<tr><td colspan=2><input type="color" ref="<?=$id;?>" class="formField" role="webColor" value="<?=$webColor;?>"></td></tr>
					<tr>
						<td>Финализирующий статус</td>
						<td style="width:10%;">
							<label class="switch"><input type="checkbox" class="formField" role="final" ref="<?=$id;?>"<?=$finalSW;?>><span class="slider round"></span></label>
						</td>
					</tr>
					<tr>
						<td>Активен</td>
						<td style="width:10%;">
							<label class="switch"><input type="checkbox" class="formField" role="active" ref="<?=$id;?>"<?=$activeSW;?>><span class="slider round"></span></label>
						</td>
					</tr>
				</tbody>