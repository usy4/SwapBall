<?php

namespace usy4\SwapBall;

use usy4\SwapBall\commands\SwapBallCommand;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\item\VanillaItems;
use pocketmine\event\Listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getCommandMap()->register($this->getName(), new SitWandsCommand($this));        
    
    }

	public function addSwapBall(Player $player, $amount){
		$item = VanillaItems::SNOWBALL()->setCount($amount);
		$item->setCustomName("§r§cSwap§cBall\n§7Shoot a player");
		$player->getInventory()->addItem($item);
                $player->sendMessage("Done.");
	}

    
	public function onDamage(EntityDamageEvent $event): void{
		$entity = $event->getEntity();
		if($entity instanceof Player){
			if($event instanceof EntityDamageByEntityEvent && ($damager = $event->getDamager()) instanceof Player){
				$item = $damager->getInventory()->getItemInHand()->getName();
				if($item == "§r§cSwap§cBall\n§7Shoot a player"){
					$event->cancel();
					$this->onHit($damager, $entity);
				}	
			}
		}
	}

	public function onHit(Player $subject, Player $targetPlayer) {
		if($subject->getName() === $subject->getName()) 
			return;			
   		$dl = $subject->getLocation();
                $el = $targetPlayer->getLocation();
                $subject->teleport($el);
                $targetPlayer->teleport($dl);
                $subject->sendMessage("§bYou swapped with §r'.$targetPlayer->getName());
	   	$targetPlayer->sendMessage('§bYou swapped with §r'.$subject->getName());
	}

}
