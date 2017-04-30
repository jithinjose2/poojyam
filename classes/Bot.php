<?php
class Bot
{

    public $game_id = 1;
    public $size = 6;
    public $boxes = array();
    public $lines = array();
    public $all_boxes = array();
    
    // return next line to be cliked by bot.
    public function nextAction($game_id){
        $this->game_id = $game_id;
        $this->getCurrentStatus();
        
        $acted_box = ""; // x,y formar of the box in which action is to be taken
        $acted_box = $this->getFirstBox(3);
        echo "pass3:$acted_box ||| ";
        if($acted_box==false){
            $acted_box = $this->getFirstBox(0);
        }
        echo "pass0:$acted_box ||| ";
        if($acted_box==false){
            $acted_box = $this->getFirstBox(1);
        }
        echo "pass1:$acted_box ||| ";
        if($acted_box==false){
            $acted_box = $this->getFirstBox(2);
        }
        echo "pass2:$acted_box ||| ";
        if($acted_box!=false){
            return $this->getUcheckedboxLine($acted_box);
        }
        return false;
    }
    
    public function getCurrentStatus(){
        $this->boxes = Game::getGameBox($this->game_id);
        $this->lines = Game::getGameActions($this->game_id);
        foreach($this->boxes as $key=>$value){
            $this->boxes[$key] = 4;
        }
        for($i=0;$i<$this->size;$i++){
            for($j=0;$j<$this->size;$j++){
                $this->ab[$i.','.$j] = $this->getBoxStatus($i,$j);
            }
        }
    }
    
    public function getBoxStatus($i,$j){
        if(isset($this->boxes[$i.",".$j])){
            return $this->boxes[$i.",".$j];
        }
        $count = 0;
        $count+= (isset($this->lines[implode(',',array($i,$j,$i+1,$j))]))? 1:0;
        $count+= (isset($this->lines[implode(',',array($i,$j,$i,$j+1))]))? 1:0;
        $count+= (isset($this->lines[implode(',',array($i+1,$j,$i+1,$j+1))]))? 1:0;
        $count+= (isset($this->lines[implode(',',array($i,$j+1,$i+1,$j+1))]))? 1:0;
        return $count;
    }
    
    public function getFirstBox($boxscore){
        $choices = array();
        for($i=0;$i<$this->size;$i++){
            for($j=0;$j<$this->size;$j++){
                if($this->ab[$i.','.$j]==$boxscore){
                    $choices[] = $i.','.$j;
                }
            }
        }
        if(count($choices)>0){
            return $choices[array_rand($choices)];
        }
        return false;
    }
    
    public function getUcheckedboxLine($box_code){
        $box_code = explode(',',$box_code);
        $i = intval($box_code[0]);
        $j = intval($box_code[1]);
        
        if(!isset($this->lines[implode(',',array($i,$j,$i+1,$j))])){
            return implode(',',array($i,$j,$i+1,$j));
        }
        if(!isset($this->lines[implode(',',array($i,$j,$i,$j+1))])){
            return implode(',',array($i,$j,$i,$j+1));
        }
        if(!isset($this->lines[implode(',',array($i,$j+1,$i+1,$j+1))])){
            return implode(',',array($i,$j+1,$i+1,$j+1));
        }
        if(!isset($this->lines[implode(',',array($i+1,$j,$i+1,$j+1))])){
            return implode(',',array($i+1,$j,$i+1,$j+1));
        }
    }
    
    
    
    //<<<<<<<<<<<<<<<<<<<<<<<<<< MASTER BRAIN2 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//
    
    // return next line to be cliked by bot.
    public function nextAction2($game_id){
        $this->game_id = $game_id;
        $this->getCurrentStatus();
        
        $acted_box = ""; // x,y formar of the box in which action is to be taken
        $acted_box = $this->getFirstBox(3);
        if($acted_box!==false){
            return $this->getUcheckedboxLine($acted_box);
        }
        
        // NO 3 check boxes,now create line coasts
        $alllines = array();
        for($i=0;$i<$this->size;$i++){
            for($j=0;$j<$this->size;$j++){
                $alllines[implode(',',array($i,$j,$i+1,$j))] = 0;
                $alllines[implode(',',array($i,$j,$i,$j+1))] = 0;
                $alllines[implode(',',array($i+1,$j,$i+1,$j+1))] = 0;
                $alllines[implode(',',array($i,$j+1,$i+1,$j+1))] = 0;
            }
        }
        
        $alllines = array_keys($alllines);
        shuffle($alllines);
        
        $clines = array();
        foreach($alllines as $tline){
            if(!isset($this->lines[$tline])){
                $cost = $this->checklineclickcost($tline,$this->lines);
                if($cost==0){
                    return $tline;
                }else{
                    $clines[$cost][] = $tline;
                }
            }
        }
        
        for($i=0;$i<40;$i++){
            if(isset($clines[$i]) && is_array($clines[$i])){
                return $clines[$i][array_rand($clines[$i])];
            }
        }
        
        return false;
    }
    
    
    // return boxes to be filled
    public function checklineclickcost($line,$clines){
        
        echo "\n checking line cost $line:";
        
        $boxes_before = $this->crossBoxCount($this->getBoxsStatus1($clines));
        
        do{
            $clines[$line] = 0;
            //print_r($clines); die();
            // fill fillable boxes
            $boxes_after = $this->crossBoxCount($this->getBoxsStatus1($clines));
            
            $line3c = false;
            if(count($boxes_after['3cliker'])){
                $i = $boxes_after['3cliker'][0];
                $j = $boxes_after['3cliker'][1];
                if(!isset($clines[implode(',',array($i,$j,$i+1,$j))])){
                    $line3c = implode(',',array($i,$j,$i+1,$j));
                }
                if(!isset($clines[implode(',',array($i,$j,$i,$j+1))])){
                    $line3c = implode(',',array($i,$j,$i,$j+1));
                }
                if(!isset($clines[implode(',',array($i+1,$j,$i+1,$j+1))])){
                    $line3c = implode(',',array($i+1,$j,$i+1,$j+1));
                }
                if(!isset($clines[implode(',',array($i,$j+1,$i+1,$j+1))])){
                    $line3c = implode(',',array($i,$j+1,$i+1,$j+1));
                }
                $line = $line3c;
            }
        }while($boxes_after[3]>0 && $line3c!==false);
        
        echo $boxes_after[4] - $boxes_before[4];
        
        return $boxes_after[4] - $boxes_before[4];
    }
    
    // fill any 3 filled boxes if any
    public function fillFillableBoxes($lines){
        for($i=0;$i<$this->size;$i++){
            for($j=0;$j<$this->size;$j++){
                $status = getBoxStatus1($i,$j,$clines);
                if($status==3){
                    $lines[implode(',',array($i,$j,$i+1,$j))] = 0;
                    $lines[implode(',',array($i,$j,$i,$j+1))] = 0;
                    $lines[implode(',',array($i+1,$j,$i+1,$j+1))] = 0;
                    $lines[implode(',',array($i,$j+1,$i+1,$j+1))] = 0;
                }
            }
        }
        return $lines;
    }
    
    public function crossBoxCount($boxes){
        $count[0] = 0;
        $count[1] = 0;
        $count[2] = 0;
        $count[3] = 0;
        $count[4] = 0;
        $count['3cliker'] = false;
        foreach($boxes as $key=>$value){
            $count[$value]++;
            if($value==3){
                $count['3cliker'] = explode(',',$key);
            }
        }
        return $count;
    }
    
    public function getBoxsStatus1($clines){
        $boxes = array();
        for($i=0;$i<$this->size;$i++){
            for($j=0;$j<$this->size;$j++){
                $boxes[$i.",".$j] = $this->getBoxStatus1($i,$j,$clines);
            }
        }
        return $boxes;
    }
    
    public function getBoxStatus1($i,$j,$clines){
        $count = 0;
        $count+= (isset($clines[implode(',',array($i,$j,$i+1,$j))]))? 1:0;
        $count+= (isset($clines[implode(',',array($i,$j,$i,$j+1))]))? 1:0;
        $count+= (isset($clines[implode(',',array($i+1,$j,$i+1,$j+1))]))? 1:0;
        $count+= (isset($clines[implode(',',array($i,$j+1,$i+1,$j+1))]))? 1:0;
        return $count;
    }

}