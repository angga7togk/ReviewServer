<?php

namespace Angga7Togk\ReviewServer;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

use Angga7Togk\ReviewServer\Form\SimpleForm;
use Angga7Togk\ReviewServer\Form\CustomForm;

class Main extends PluginBase implements Listener{
    
    public Config $config;
    public Config $dt;
    
    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->saveResource("data.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->dt = new Config($this->getDataFolder() . "data.yml", Config::YAML, array());
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label,  array $args) : bool{
        
        if($cmd->getName() == "review"){
            if($sender instanceof Player){
                $this->dt->reload();
                $this->config->reload();
                $this->ReviewUI($sender);
                return true;
            }
        }
    }
    
    public function ReviewUI($player){
        $form = new SimpleForm(function($player, $data = null){
            if($data === null){
                return true;
            }
            switch($data){
                case 0:
                    $this->CreateReview($player);
                break;
                
                case 1:
                    $this->ReviewList($player);
                break;
            }
        });
        $form->setTitle($this->config->get("Menu")["Title"]);
        $form->setContent($this->config->get("Menu")["Content"]);
        $form->addButton($this->config->get("Menu")["Button1"]);
        $form->addButton($this->config->get("Menu")["Button2"]);
        $form->addButton($this->config->get("Menu")["Button-Exit"]);
        $form->sendToPlayer($player);
        return $form;
    }
    
    public function CreateReview($player){
        $form = new CustomForm(function($player, $data){
            if($data === null){
                $this->ReviewUI($player);
                return true;
            }
            $this->dt->setNested("list", $player->getName() . "\n  " . $data[1] . "\n\n" . $this->dt->get("list"));
            $player->sendMessage("Your Create Review: ". $data[1]);
            $this->dt->save();
            $this->dt->reload();
        });
        $form->setTitle($this->config->get("title-cr"));
        $form->addLabel($this->config->get("content-cr"));
        $form->addInput("Review:", "Exaple: good server");
        $form->sendToPlayer($player);
        return $form;
    }
    
    public function ReviewList($player){
        $form = new SimpleForm(function($player, $data = null){
            if($data === null){
                $this->ReviewUI($player);
                return true;
            }
            if($data === 0){
                $this->ReviewUI($player);
                return true;
            }
        });
        $form->setTitle($this->config->get("title-rl"));
        $form->setContent($this->dt->get("list"));
        $form->addButton($this->config->get("button-rl"));
        $form->SendToPlayer($player);
        return $form;
    }
}
