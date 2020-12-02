				<tr>
					<td ref="<?=$id;?>" class="settingCaption"><?=$event_name;?></td>
					<td class="settingCaptionBg">
						<button class="editItem pull-right btn btn-small" ref="<?=$id;?>"><?=$buttonLabel;?></button>
						<button class="saveItem pull-right btn btn-small hide" ref="<?=$id;?>">Сохранить</button>
					</td>
				</tr>
				<tbody ref="<?=$id;?>" class="settingSection hide">
				<tr>
					<td colspan=2>Название</td>
				</tr>
				<tr>
					<td colspan=2><textarea rows="3" cols="25" ref="<?=$id;?>" role="eventName" class="formField"><?=$event_name;?></textarea></td>
				</tr>
				<tr>
					<td colspan=2>Тема сообщения</td>
				</tr>
				<tr>
					<td colspan=2><input type="text" ref="<?=$id;?>" role="subject" class="formField" value="<?=$subject;?>"></td>
				</tr>
				<tr>
					<td colspan=2>Текст сообщения</td>
				</tr>
				<tr>
					<td colspan=2><textarea rows="3" cols="45" ref="<?=$id;?>" class="formField" role="text"><?=$text;?></textarea></td>
				</tr>
				<tr>
					<td colspan=2>URL ссылки</td>
				</tr>
				<tr>
					<td colspan=2><input type="text" ref="<?=$id;?>" role="link" class="formField" value="<?=$link;?>"></td>
				</tr>
				<tr>
					<td colspan=2>Текст ссылки</td>
				</tr>
				<tr>
					<td colspan=2><input type="text" ref="<?=$id;?>" role="link_text" class="formField" value="<?=$link_text;?>"></td>
				</tr>
				<tr>
					<td colspan=2>Обратный адрес</td>
				</tr>
				<tr>
					<td colspan=2><input type="text" ref="<?=$id;?>" role="from_email" class="formField" value="<?=$from_email;?>"></td>
				</tr>
				<tr>
					<td>Активен</td>
					<td style="width:10%;">
						<label class="switch"><input type="checkbox" class="formField" role="active" ref="<?=$id;?>"<?=$activeSW;?>><span class="slider round"></span></label>
					</td>
				</tr>
				</tbody>