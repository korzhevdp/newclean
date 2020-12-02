				<tr ref="<?=$id;?>" class="settingCaptionRow">
					<td ref="<?=$id;?>" class="settingCaption"><?=$name;?></td>
					<td class="settingCaptionBg">
						<button class="editItem pull-right btn btn-small" ref="<?=$id;?>"><?=$buttonLabel;?></button>
						<button class="saveItem pull-right btn btn-small hide" ref="<?=$id;?>">Сохранить</button>
					</td>
				</tr>
				<tbody ref="<?=$id;?>" class="settingSection hide">
					<tr>
						<td colspan=2>Имя группы</td>
					</tr>
					<tr>
						<td colspan=2><input type="text" class="formField" role="name" ref="<?=$id;?>" value="<?=$name;?>"></td>
					</tr>
					<tr>
						<td colspan=2>Название роли</td>
					</tr>
					<tr>
						<td colspan=2><input type="text" class="formField" role="caption" ref="<?=$id;?>" value="<?=$caption;?>"></td>
					</tr>
					<tr>
						<td><?=$rights['law1'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law1" ref="<?=$id;?>" <?=(($law1)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law2'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law2" ref="<?=$id;?>" <?=(($law2)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law3'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law3" ref="<?=$id;?>" <?=(($law3)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law4'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law4" ref="<?=$id;?>" <?=(($law4)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law5'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law5" ref="<?=$id;?>" <?=(($law5)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law6'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law6" ref="<?=$id;?>" <?=(($law6)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law7'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law7" ref="<?=$id;?>" <?=(($law7)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law8'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law8" ref="<?=$id;?>" <?=(($law8)    ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					<tr>
						<td><?=$rights['law9'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law9" ref="<?=$id;?>" <?=(($law9)    ? 'checked' : '' );?>><span class="slider round"></span></label</td>
					</tr>
					<tr>
						<td><?=$rights['law10'];?></td>
						<td><label class="switch"><input type="checkbox" class="formField" role="law4_1" ref="<?=$id;?>" <?=(($law10) ? 'checked' : '' );?>><span class="slider round"></span></label></td>
					</tr>
					</tbody>