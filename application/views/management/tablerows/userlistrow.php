				<tr class="<?=$executive;?>">
					<td><input type="text" ref="<?=$id;?>" value="<?=$alias;?>" class="formField" role="alias"></td>
					<td><input type="text" ref="<?=$id;?>" value="<?=$email;?>" class="formField" role="email"></td>
					<td><input type="text" ref="<?=$id;?>" value="<?=$phone;?>" class="formField" role="phone"></td>
					<td><select ref="<?=$id;?>" class="formField" role="groupID"><?=$groupList;?></select></td>
					<td><select ref="<?=$id;?>"<?=$disabled;?> class="formField" role="departmentID"><?=$departmentsList;?></select></td>
					<td><select ref="<?=$id;?>"<?=$disabled;?> class="formField" role="organizationID"><?=$organizationsList;?></select></td>
					<td><?=$registrationDate;?></td>
					<td><?=$authDate;?></td>
					<td>
						<label class="switch"><input type="checkbox" class="slideSwitcher formField" role="active" ref="<?=$id;?>"<?=$activeSW;?>><span class="slider round"></span></label>
					</td>
					<td><button class="saveItem pull-right btn btn-small" ref="<?=$id;?>">Сохранить</button></td>
				</tr>