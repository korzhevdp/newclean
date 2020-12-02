				<tr class="settingCaptionRow" ref="<?=$id;?>">
					<td ref="<?=$id;?>" class="settingCaption"><?=$name;?></td>
					<td class="settingCaptionBg">
						<button class="editItem pull-right btn btn-small" ref="<?=$id;?>"><?=$buttonLabel;?></button>
						<button class="saveItem pull-right btn btn-small hide" ref="<?=$id;?>">Сохранить</button>
					</td>
				</tr>
				<tbody ref="<?=$id;?>" class="settingSection hide <?=$ifDepartment;?>">
					<tr><th colspan=2>Наименование</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$name;?>" class="formField" role="name"></td></tr>
					<tr><th colspan=2>Полное наименование</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$full_name;?>" class="formField" role="fullName"></td></tr>
					<tr><th colspan=2>Адрес</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$address;?>" class="formField" role="address"></td></tr>
					<tr><th colspan=2>ИНН</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$inn;?>" class="formField" role="inn"></td></tr>
					<tr><th colspan=2>Телефон</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$phone;?>" class="formField" role="phone"></td></tr>
					<tr><th colspan=2>Электронная почта</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$email;?>" class="formField" role="email"></td></tr>
					<tr><th colspan=2>Руководитель</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$boss;?>" class="formField" role="boss"></td></tr>
					<tr><th colspan=2>Количество домов</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$house_count;?> "class="formField" role="houseCount"></td></tr>
					<tr><th colspan=2>Количество персонала</th></tr>
					<tr><td colspan=2><input type="text" ref="<?=$id;?>" value="<?=$personal_count;?>" class="formField" role="personnelCount"></td></tr>
					<tr>
						<td>Подразделение Администрации города</td>
						<td style="width:10%;">
							<label class="switch"><input type="checkbox" class="formField" ref="<?=$id;?>" role="department" <?=$departmentSW;?>><span class="slider round"></span></label>
						</td>
					</tr>
					<tr>
						<td>Активен</td>
						<td style="width:10%;">
							<label class="switch"><input type="checkbox" class="formField" ref="<?=$id;?>" role="active" <?=$activeSW;?>><span class="slider round"></span></label>
						</td>
					</tr>
				</tbody>