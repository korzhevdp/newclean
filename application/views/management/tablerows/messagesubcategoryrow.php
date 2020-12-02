				<tr ref="<?=$id;?>" class="<?=$inactiveClass?> settingCaptionRow">
					<td ref="<?=$id;?>" class="settingCaption"><?=$parentName.$name;?></td>
					<td class="settingCaptionBg">
						<button class="editItem pull-right btn btn-small" ref="<?=$id;?>"><?=$buttonLabel;?></button>
						<button class="saveItem pull-right btn btn-small hide" ref="<?=$id;?>">Сохранить</button>
					</td>
				</tr>
				<tbody ref="<?=$id;?>" class="settingSection hide<?=$inactiveClass;?>">
					<tr><th colspan=2>Название</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="name" value="<?=$name;?>"></td></tr>
					<tr><th colspan=2>Заголовок</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="caption" value="<?=$caption;?>"></td></tr>
					<tr><th colspan=2>Родительская категория</th></tr>
					<tr><td colspan=2><select ref="<?=$id;?>" class="formField" role="category"><?=$categoriesList;?></select></td></tr>
					<tr><th colspan=2>Описание</th></tr>
					<tr><td colspan=2><textarea ref="<?=$id;?>" rows="3" cols="25" class="formField" role="description"><?=$description;?></textarea></td></tr>
					<tr><th colspan=2>Дедлайн, дни</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="deadline" value="<?=$deadline;?>"></td></tr>
					<tr><th colspan=2>Иконка</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="icon" value="<?=$icon;?>"></td></tr>
					<tr><th colspan=2>Иконка Яндекс</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" class="formField" role="yandex_icon" value="<?=$yandex_icon;?>"></td></tr>
					<tr><th colspan=2>Контролирует</th></tr>
					<tr><td colspan=2><select ref="<?=$id;?>" class="formField" role="department"><?=$departmentsList;?></select></td></tr>
					<tr><th colspan=2>Организация</th></tr>
					<tr><td colspan=2><select ref="<?=$id;?>" class="formField" role="organization"><?=$organizationsList;?></select></td></tr>
					<tr><th colspan=2>Округ</th></tr>
					<tr><td colspan=2><select ref="<?=$id;?>" class="formField" role="district"><?=$districtsList;?></select></td></tr>
					<tr>
						<td>Активен</td>
						<td style="width:10%;">
							<label class="switch"><input type="checkbox" class="formField" role="active" ref="<?=$id;?>"<?=$activeSW;?>><span class="slider round"></span></label>
						</td>
					</tr>
				</tbody>