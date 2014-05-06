<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function regex_replace_install()
{
	
}

function regex_replace_remove()
{
	
}

function regex_replace_menu()
{
	add_options_page('查找替换页面', '查找替换', 'administrator', 'display_regex_replace', 'regex_replace_page');
}

function regex_replace_action()
{
	if (isset($_POST['regex_replace_action']))
	{
		$message = '';
		//echo 'check_admin_referer';
		check_admin_referer('regex_replace_nonce');
		//echo 'regex_replace_nonce';
		if (empty($_POST['regex_replace_search']))
		{
			$message .= '<div class="error"><p><strong>&raquo; ' . __('please enter the text you want to search!', FB_SAR_TEXTDOMAIN) . '</strong></p></div><br class="clear">';
		}
		else
		{
			if (is_regex($_POST['regex_replace_search']))
				$message .= regex_replace_action_do_regex($_POST['regex_replace_search'], $_POST['regex_replace_replace'], isset($_POST['contain_title']), isset($_POST['contain_content']));
			else
				$message .= regex_replace_action_do($_POST['regex_replace_search'], $_POST['regex_replace_replace'], isset($_POST['contain_title']), isset($_POST['contain_content']));
		}
		echo $message;
	}
}

function regex_replace_action_do($search, $replace, $contain_title, $contain_content)
{
	global $wpdb;
	if ($contain_title)
	{
		$query = "UPDATE $wpdb->posts ";
		$query .= "SET post_title = ";
		$query .= "REPLACE(post_title, \"$search\", \"$replace\") ";
		$wpdb->get_results($query);
	}
	if ($contain_content)
	{
		$query = "UPDATE $wpdb->posts ";
		$query .= "SET post_content = ";
		$query .= "REPLACE(post_content, \"$search\", \"$replace\") ";
		$wpdb->get_results($query);
	}

	$message .= ' all replaced ! <br />';
	return $message;
}

function regex_replace_action_do_regex($search, $replace, $contain_title, $contain_content)
{
	global $wpdb;
	$page_size = 100;
	$page = 0;
	$message = '';
	while (true)
	{
		$offset = $page * $page_size;

		$sql = "SELECT ID,post_content,post_title
				FROM $wpdb->posts  
				LIMIT $offset,$page_size ";
		$rows = $wpdb->get_results($sql);

		if (empty($rows))
			break;
		$message .= "offset:$offset  begin <br />";

		foreach ($rows as $row)
		{
			if ($contain_title && preg_match($search, $row->post_content))
			{
				$row->post_content = preg_replace($search, $replace, $row->post_content);
				$query = $wpdb->prepare("UPDATE $wpdb->posts SET post_content = %s WHERE ID=%d", $row->post_content, $row->ID);
				$wpdb->query($query);
				$message .= "post:  $row->ID title replaced <br />";
				break;
			}
			if ($contain_content && preg_match($search, $row->post_content))
			{
				$row->post_content = preg_replace($search, $replace, $row->post_content);
				$query = $wpdb->prepare("UPDATE $wpdb->posts SET post_content = %s WHERE ID=%d", $row->post_content, $row->ID);
				$wpdb->query($query);
				$message .= "post:  $row->ID content replaced <br />";
				break;
			}
		}
		$message .= "offset:$offset  end <br />";
		//分析下一批数据 
		$page ++;
	}
	$message .= ' all replaced ! <br />';
	return $message;
}

function starts_with($haystack, $needle)
{
	return $needle === "" || strpos($haystack, $needle) === 0;
}

function ends_with($haystack, $needle)
{
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function is_regex($str)
{
	if (empty($str))
		return false;

	if (starts_with($str, '/'))
	{
		if (ends_with($str, '/') || ends_with($str, '/i') || ends_with($str, '/g') || ends_with($str, '/m') || ends_with($str, '/gi') || ends_with($str, '/ig')
		)
			return true;
		else
			return false;
	}
	return false;
}

function regex_replace_page()
{
	?>  
	<div>  
		<h2>查找替换</h2>  
		<form method="post" >  
			<?php
			//执行
			regex_replace_action();
			?>  
	<?php wp_nonce_field('regex_replace_nonce'); ?>  
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="blogname">查找</label></th>
						<td><textarea  
								name="regex_replace_search" 
								id="regex_replace_search" 
								cols="40" 
								rows="2"></textarea> </td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="blogname">替换为</label></th>
						<td> <textarea  
								name="regex_replace_replace" 
								id="regex_replace_replace" 
								cols="40" 
								rows="2"></textarea>  </td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="blogname">替换为</label></th>
						<td> 
							<input type='checkbox' name='contain_title' id='title_label' />
							<label for="title_label">标题</label>
							<br />
							<input type='checkbox' name='contain_content' id='content_label' />
							<label for="content_label">内容</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="blogname"></label></th>
						<td> <input type="hidden" name="regex_replace_action" value="" />    
							<input type="submit" value=" 执行 " class="button-primary" onclick="return confirm('执行后无法恢复，建议先备份数据库，确认继续执行吗?');" />    </td>
					</tr>

				</tbody>
			</table> 
		</form>  
	</div>  
	<?php
}
