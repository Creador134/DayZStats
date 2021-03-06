<?php
	session_start();
	include ('queries.php');
	require_once ('config.php');

	$CharacterID = '';
	if (isset($_REQUEST['CharacterID']) && $_REQUEST['CharacterID'] > 0){
		$CharacterID = $_REQUEST['CharacterID'];
	} else {
		$CharacterID = 0;
	}

	$row = $db->GetRow($info1, $CharacterID);

	$MapCoords = worldspaceToMapCoords($row['Worldspace']);
	$Inventory = $row['Inventory'];
	$Inventory = str_replace("|", ",", $Inventory);
	$Inventory  = json_decode($Inventory);

	$Backpack  = $row['Backpack'];
	$Backpack = str_replace("|", ",", $Backpack);
	$Backpack  = json_decode($Backpack);
	$model = $row['Model'];
    // PlayerDATA.playerSex isn't used
    if($model == 'SurvivorW2_DZ' || $model == 'BanditW1_DZ') {
        $playerSex = 1;
    } else {
        $playerSex = 0;
    }
	
	$Medical = $row['Medical'];
	$Medical = str_replace("|", ",", $Medical);
	$Medical = json_decode($Medical);

	$binocular = array();
    $rifle = "";
    $carry = "";
    $pistol = "";
	$heavyammo = array();
	$heavyammoslots = 0;
	$smallammo = array();
	$usableitems = array();
	$survival_time = survivalTimeToString($row['duration']);

	$xml = file_get_contents('items.xml', true);
	require_once('modules/xml2array.php');
	$items_xml = XML2Array::createArray($xml);

	if (isset($Inventory[2]) && $Inventory[2] != ''){
		$InvCarry = array($Inventory[2]); 
	} else {
		$InvCarry = array();
	}

    $weaponinhand = "";
    if(isset($Inventory[0][0])) {
        $weaponinhand = $Inventory[0][0];
    }
	$Inventory = (array_merge($Inventory[0], $Inventory[1], $InvCarry));
	for ($i=0; $i<count($Inventory); $i++){
		if(array_key_exists($i,$Inventory)){
			$curitem = $Inventory[$i];
			$icount = "";
			if (is_array($curitem)){$curitem = $Inventory[$i][0]; $icount = ' - '.$Inventory[$i][1].' rounds'; }
			if(array_key_exists('s'.$curitem,$items_xml['items'])){
				switch($items_xml['items']['s'.$curitem]['Type']){
					case 'binocular':
						$binocular[] = '<img style="margin-left: 2px;margin-top: 12px;max-width:76px;max-height:76px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
						break;
					case 'rifle':
                        if($curitem == $InvCarry[0]) {
						    $carry = '<img style="max-width:220px;max-height:92px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
                        } else {
						    $rifle = '<img style="max-width:220px;max-height:92px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
                        }
						break;
					case 'carry':
                        if($curitem == $InvCarry[0]) {
						    $carry = '<img style="max-width:220px;max-height:92px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
                        } else {
						    $rifle = '<img style="max-width:220px;max-height:92px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
                        }
						break;
					case 'pistol':
						$pistol = '<img style="margin-top: 10px;max-width:76px;max-height:76px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
						break;
					case 'backpack':
						break;
					case 'heavyammo':
						$heavyammo[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$icount.'" alt="'.$curitem.$icount.'"/>', 'slots' => $items_xml['items']['s'.$curitem]['Slots']);
						break;
					case 'smallammo':
						$smallammo[] = '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$icount.'" alt="'.$curitem.$icount.'"/>';
						break;
					case 'item':
						$usableitems[] = '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.'" alt="'.$curitem.'"/>';
						break;
					default:
						$s = '';
				} 
			} else {
				$debug .= 'Unknown item:&nbsp;'.$curitem.';<br />';
			}
		}
	}	


?>	
<div id="page-heading">
	<center>
		<h3><?php echo "<title>".htmlspecialchars($row['playerName'])." - ".$sitename."</title>"; ?></h3>
		<h3 class="custom-h3"><?php echo htmlspecialchars($row['playerName']); ?> - <?php echo $row['CharacterID']; ?> - Last save: <?php echo $row['last_updated']; ?></h3>
	</center>
</div>
<!-- end page-heading -->

<table border="0" width="100%" cellpadding="0" cellspacing="0" id="content-table">
<tr>
			<div id="gear_player" style="margin-left: 64px; margin-bottom: 10px;">	
				<div class="gear_info">
					<img class="playermodel" src='images/models/<?php echo str_replace('"', '', $model); ?>.png'/>
					<div id="small-stats" style="margin-left:-60px;margin-top:450px">
						
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-120px">
						<?php echo 'Zed kills:&nbsp;'.$row['KillsZ'];?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-105px">
						<?php echo 'Zed headshots:&nbsp;'.$row['HeadshotsZ'];?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-90px">
						<?php echo 'Human killed:&nbsp;'.$row['KillsH'];?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-75px">
						<?php echo 'Bandit killed:&nbsp;'.$row['KillsB'];?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-60px">
						<?php echo 'Survival Attempts:&nbsp;'.$row['Generation'];?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-45px">
						<?php echo 'Survival Time:&nbsp;'.$survival_time;?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-30px">
						<?php echo 'Humanity:&nbsp;'.$row['Humanity'];?>
					</div>
					<div class="statstext" style="width:180px;margin-left:280px;margin-top:-15px">
						<?php echo 'PlayerID:&nbsp;'.$row['PlayerUID'];?>
					</div>
					</div>
				</div>
				<div class="gear_inventory">
					<div class="gear_bino1_slot" style="margin-left:295px;margin-top:340px;width:100px;height:100px;">
					<?php
						if(array_key_exists(0,$binocular)){
							echo $binocular[0];
						} else {
							echo '<img style="max-width:78px;max-height:78px;" src="images/gear/binocular.png" title="" alt=""/>';
						}
					?>
					</div>
					<div class="gear_bino2_slot" style="margin-left:295px;margin-top:438px;width:100px;height:100px;">
					<?php
						if(array_key_exists(1,$binocular)){
							echo $binocular[1];
						} else {
							echo '<img style="max-width:78px;max-height:78px;" src="images/gear/binocular.png" title="" alt=""/>';
						}
					?>
					</div>
					<div class="gear_holding_slot" style="margin-left:99px;margin-top:146px;width:249px;height:97px;">
						<?php
							echo $rifle;
						?>
					</div>
					<div class="gear_carry_slot" style="margin-left:99px;margin-top:244px;width:249px;height:97px;">
						<?php
							echo $carry;
						?>
					</div>
					<div class="gear_bag_slot" style="margin-left:99px;margin-top:48px;width:249px;height:97px;">
					<?php					
						if(isset($Backpack[0]) && $Backpack[0] != ''){
							echo '<img style="max-width:220px; max-height:92px;" src="images/thumbs/'.$Backpack[0].'.png" title="'.$Backpack[0].'" alt="'.$Backpack[0].'"/>';
						}
					?>
					</div>
					<div class="gear_pistol_slot" style="margin-left:1px;margin-top:342px;width:100px;height:100px;">
					<?php
						echo $pistol;
					?>
					</div>
					<?php					
						$jx = 1;
						$jy = 48;
						$jk = 0;
						$jl = 0;
						$maxslots = 12;
						for ($j=0; $j<$maxslots; $j++){
                                while($jk > 1){
                                $jk -= 2;
                                $jl++;
                            }
							//big ammo
							$hammo = '';
							if ($j > 5){
								$hammo = '';
							}
							if(array_key_exists($j,$heavyammo)){
								$hammo = $heavyammo[$j]['image'];									
								echo '<div class="gear_slot" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;">'.$hammo.'</div>';
								$jk = $jk - 1 + $heavyammo[$j]['slots'];
								$heavyammoslots = $heavyammoslots + $heavyammo[$j]['slots'];
							} else {
								if($heavyammoslots==$maxslots){
									break;
								}
								$heavyammoslots++;
								
								echo '<div class="gear_slot" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;">'.$hammo.'
							</div>';
							}
							$jk++;
							
						}
						$jx = 99;
						$jy = 342;
						$jk = 0;
						$jl = 0;
						for ($j=0; $j<8; $j++){
							if ($jk > 3){ $jk = 0;$jl++;}
							//small ammo
							$sammo = '';
							if(array_key_exists($j,$smallammo)){
								$sammo = $smallammo[$j];
							}
							echo '<div class="gear_pistol_slots" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;">'.$sammo.'
							</div>';								
							$jk++;
						}
						$jx = 1;
						$jy = 440;
						$jk = 0;
						$jl = 0;
						for ($j=0; $j<12; $j++){
							if ($jk > 5){ $jk = 0;$jl++;}
							//items
							$uitem = '';
							if(array_key_exists($j,$usableitems)){
								$uitem = $usableitems[$j];
							}
							echo '<div class="gear_item_slots" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;">'.$uitem.'
							</div>';								
							$jk++;
						}
					?>
				</div>
				<!-- Backpack -->
				<div class="gear_backpack">						
					<?php
						if(isset($Backpack[1][0]) && count($Backpack[1][0]) == NULL){
							$maxmagazines = 0;
						} else {
							$maxmagazines = 24;
						}
						$BackpackName = $Backpack[0];
						if(array_key_exists('s'.$Backpack[0],$items_xml['items'])){
							$maxmagazines = $items_xml['items']['s'.$Backpack[0]]['maxmagazines'];
						}
						
						$bpweapons = array();
						if(array_key_exists(0, $Backpack[1])){
							$bpweaponscount = count($Backpack[1][0]);							
							for ($m=0; $m<$bpweaponscount; $m++){
									for ($mi=0; $mi<$Backpack[1][1][$m]; $mi++){
										$bpweapons[] = $Backpack[1][0][$m];
									}
							}
						}

						
						$bpitems = array();
						if(array_key_exists(0, $Backpack[2])){
							$bpitemscount = count($Backpack[2][0]);							
							for ($m=0; $m<$bpitemscount; $m++){
								for ($mi=0; $mi<$Backpack[2][1][$m]; $mi++){
									$bpitems[] = $Backpack[2][0][$m];
								}
							}
						}
						
						$Backpack = (array_merge($bpweapons, $bpitems));
						
						$backpackslots = 0;
						$backpackitem = array();
						$bpweapons = array();
						for ($i=0; $i<count($Backpack); $i++){
							if(array_key_exists('s'.$Backpack[$i],$items_xml['items'])){
								switch($items_xml['items']['s'.$Backpack[$i]]['Type']){
									case 'binocular':
										$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									case 'rifle':
										$bpweapons[] = array('image' => '<img style="max-width:124px;max-height:92px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									case 'carry':
										$bpweapons[] = array('image' => '<img style="max-width:124px;max-height:92px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									case 'pistol':
										$bpweapons[] = array('image' => '<img style="max-width:92px;max-height:92px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									case 'backpack':
										break;
									case 'heavyammo':
										$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									case 'smallammo':
										$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									case 'item':
										$backpackitem[] = array('image' => '<img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$Backpack[$i].'.png" title="'.$Backpack[$i].'" alt="'.$Backpack[$i].'"/>', 'slots' => $items_xml['items']['s'.$Backpack[$i]]['Slots']);
										break;
									default:
										$s = '';
								}
							}
						}	
						
						$weapons = count($bpweapons);
						$magazines = $maxmagazines;
						$freeslots = $magazines;
						$jx = 1;
						$jy = 48;
						$jk = 0;
						$jl = 0;
						for ($j=0; $j< $weapons; $j++){
							if ($jk > 1){ $jk = 0;$jl++;}
							echo '<div class="gear_slot" style="margin-left:'.($jx+(130*$jk)).'px;margin-top:'.($jy+(98*$jl)).'px;width:128px;height:96px;">'.$bpweapons[$j]['image'].'</div>';
							$magazines = $magazines - $bpweapons[$j]['slots'];	
							$freeslots = $freeslots - $magazines;
							$jk++;
						}
						
						
						$jx = 1;
						$jy = 48 + (98*round($weapons/2));
						$jk = 0;
						$jl = 0;

						for ($j=0; $j<$magazines; $j++){
							if ($jk > 7){ $jk = 0;$jl++;}
							if ($j<count($backpackitem)){
								echo '<div class="gear_slot" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;">'.$backpackitem[$j]['image'].'</div>';
								$jk = $jk - 1 + $backpackitem[$j]['slots'];
								$backpackslots = $backpackslots + $backpackitem[$j]['slots'];
								$freeslots = $freeslots - $backpackitem[$j]['slots'];
							} else {
								if($backpackslots==$maxmagazines){
									break;
								}
								$backpackslots++;
								echo '<div class="gear_slot" style="margin-left:'.($jx+(49*$jk)).'px;margin-top:'.($jy+(49*$jl)).'px;width:47px;height:47px;"></div>';
							}								
							$jk++;
						}	 			
					?>
					<div class="backpackname">
					<?php
						echo $BackpackName.'&nbsp;&nbsp;(&nbsp;'.$freeslots.'&nbsp;/&nbsp;'.$maxmagazines.'&nbsp;)';
					?>
					</div>
				</div>
				<!-- Backpack -->
			</div>			
		<?php
		echo $debug;
		?>
	</td>
</tr>
</table>
