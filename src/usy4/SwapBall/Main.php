<?php

namespace usy4\SwapBall;

use usy4\SwapBall\commands\SwapBallCommand;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\item\VanillaItems;
use pocketmine\event\Listener;

use pocketmine\entity\projectile\Snowball;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\player\PlayerItemUseEvent;

class Main extends PluginBase implements Listener{

    public function onEnable() : void{
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	$this->getServer()->getCommandMap()->register($this->getName(), new SwapBallCommand($this));        
        $this->SwapBallDespawn();
    }
    
    public function SwapBallDespawn(){
        foreach($this->getServer()->getWorldManager()->getWorlds() as $world){
            foreach($world->getEntities() as $entity) {
                if($entity instanceof Snowball) {
                    $entity->flagForDespawn();
                }
            }
        }
    }

    public function addSwapBall(Player $player, $amount){
	$item = VanillaItems::SNOWBALL()->setCount($amount);
	$item->setCustomName("§r§cSwap§bBall\n§7Shoot a player");
	$player->getInventory()->addItem($item);
        $player->sendMessage("Done.");	
    }
    
    public function onLaunch(ProjectileLaunchEvent $event){  
        $entity = $event->getEntity();
        $player = $entity->getOwningEntity();
	if($player instanceof Player){
	    if($player->getInventory()->getItemInHand()->getName() == "§r§cSwap§bBall\n§7Shoot a player"){
		$entity->setNameTag("SwapBall");  
	    }
        }
    }
    
    /**
       * @ignoreCancelled true
       * @priority MONITOR
         */
    
    public function onHit(ProjectileHitEvent $event) : void{
        $entity = $event->getEntity();
        $owner = $entity->getOwningEntity();
        $et = $entity->getNameTag();	 
        if($event instanceof ProjectileHitEntityEvent && ($target = $event->getEntityHit()) instanceof Player){         
            if($et == "SwapBall"){
                $this->Hit($owner, $target);
            }		
        }
    }
    
    public function Hit(Player $subject, Player $targetPlayer) {
	if($subject->getName() === $targetPlayer->getName()) 
		return;			
   	$dl = $subject->getLocation();
        $el = $targetPlayer->getLocation();
        $subject->teleport($el);
        $targetPlayer->teleport($dl);
        $subject->sendMessage('§bYou swapped with §r'.$targetPlayer->getName());
	$targetPlayer->sendMessage('§bYou swapped with §r'.$subject->getName());
    }
    
    public function onChild(EntityDamageByChildEntityEvent $event){
        if($event->getChild()->getNameTag() == "SwapBall"){
            $event->cancel();
        }
    }
        
}
