<?php

// +----------------------------------------------------------------------+
// | Perfect Scripts                                     class.pcrypt.php |
// | Brazilian Organization                                               |
// +----------------------------------------------------------------------+
// | Viva ao Linux!                                                       |
// | Porque nós amamos a liberdade!                                       |
// +----------------------------------------------------------------------+
// | Class Perfect Crypt                                                  |
// | Created By Igor Ribeiro de Assis                                     |
// | <igor21@terra.com.br> UIN: 71064682                                  |
// +----------------------------------------------------------------------+
// | http://ps.wmforce.com                                                |
// +----------------------------------------------------------------------+
// | Under GPL                                                            |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published    |
// | by the Free Software Foundation; either version 2 of the License,    |
// | or (at your option) any later version.                               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+

/**
* Encryption Block Mode constants
*
* @const MODE_ECB ecb mode
* @const MODE_CBC cbc mode
*/
define("MODE_ECB",0);
define("MODE_CBC",1);

/**
  * An abstract layer class to encrypt data.
  *
  * This class is class is used to encrypt and decrypt data using different algorithms
  * and modes.
  *
  * @author  Igor Ribeiro de Assis <igor21@terra.com.br>
  * @version 1.0-beta
  * @access  public
  * @package Perfect Crypt
  */

class pcrypt
{
    /** Encryption Block Mode: ECB, CBC actually.
      *
      * @var    int the mode used 
      * @access private
      */
    var $blockmode = MODE_ECB;
    
    /** Key for Encryption
      *
      * @var    string the key used in encryption and decryption
      * @access public
      */
    var $key = null;

    /** IV - Initialization Vector String
      *
      * @var    string initialization vector for some modes (CBC)
      * @access public
      */
    var $iv  = "z4c8e7gh"; 
    
    /** Methods */
    
    /** Constructor of the class.
      * 
      * The constructor initialize some important vars and include the algorithm
      * file.
      *
      * @access public
      * @param  int $blockmode the blockmode to use
      * @param  string $cipher the algorithm used to crypt
      * @param  string $key the ley used to crypt
      * 
      * @return void
      */
    
    function pcrypt($blockmode = MODE_ECB, $cipher = 'BLOWFISH', $key = null)
    {
        // Include cipher_class file
        $cipher = strtolower($cipher);
        if (!file_exists($cipher.".php")) 
        {
            //$this->error();
        }
        include_once $cipher.".php";
        
        // Load cipher_class
        if (!class_exists("pcrypt_".$cipher))
        {
            $this->error("Class pcrypt_".$cipher." doesn't exists");
        }
        
        $class  = "pcrypt_".$cipher;
        $this->cipher = new $class($key);
        
        // Initialize Vars
        $this->blockmode = $blockmode;
        $this->key = $key;
    }
    
    /** Crypt data using the selected algorithm
      * 
      * This method encrypt data using the selected algorithm and mode:
      *    Algorithms: Blowfish
      *    Modes: ECB, CBC
      * For a description about algorithms and modes see: 
      * Applied Cryptography by Bruce Schneier
      *
      * @access public
      * @param  string $plain  the plain text to be encrypted
      * @return string $cipher the plain text encrypted
      */
    function encrypt($plain)
    {
        if (empty($plain))
        {
            $this->error("Empty Plain Text");
        }
        
        // Encrypt using the correct mode
        switch($this->blockmode) {
        case MODE_ECB:
            $cipher = $this->_ecb_encrypt($plain);
            break;
        
        case MODE_CBC:
            $cipher = $this->_cbc_encrypt($plain);
            break;

        default:
            $this->error("Invalid mode ".$this->blockmode);
        }
        
        return $cipher;
    }

    /** Decrypt using the selected algorithm
      *
      * This method decrypt data using the selected algorithm and mode.
      * TODO: Discover the algorithm and mode auto
      *
      * @access public
      * @param  string $cipher the crypted data to be decrypted
      * @return string $plain  the cipher text decrypted
      */
    function decrypt($cipher)
    {
        if (empty($cipher))
        {
            $this->error("Invalid Cipher Text");
        }
        
        // Decrypt with the correct mode        
        switch($this->blockmode) {
        case MODE_ECB:
            $plain = $this->_ecb_decrypt($cipher);
            break;
        
        case MODE_CBC:
            $plain = $this->_cbc_decrypt($cipher);
            break;

        default:
            $this->error("Invalid mode ".$this->blockmode);
        }
        
        return $plain;
    }
    
    /** Method to encrypt using ECB mode.
      * 
      * In ECB mode the blocks are encrypted independently 
      *
      * @access private
      * @param  string $plain  the plain text to be encrypted
      * @return string $cipher the plain text encrypted
      */
    function _ecb_encrypt($plain)
    {
        $blocksize = $this->cipher->blocksize;
        $plainsize = strlen($plain);
        $cipher    = '';
       
        for($i = 0;$i < $plainsize;$i = $i + $blocksize)
        {
            $block = substr($plain,$i,$blocksize); 
            
            if(strlen($block) < $blocksize)
            {
                // pad block with '\0'
                $block = str_pad($block,$blocksize,"\0",STR_PAD_LEFT);
            }
            $cipher .= $this->cipher->_encrypt($block);
        }
        
        return $cipher;
    }
    
    /** Method to decrypt using ECB mode.
      *
      * @access private
      * @param  string $cipher the cipher text
      * @return string $plain  the cipher text decrypted
      */
    function _ecb_decrypt($cipher)
    {
        $blocksize  = $this->cipher->blocksize;
        $ciphersize = strlen($cipher);
        $plain      = ''; 
        
        for($i = 0;$i < $ciphersize;$i = $i + $blocksize)
        {
            $block = substr($cipher,$i,$blocksize); 
            $block = $this->cipher->_decrypt($block);
            
            // Remove padded chars 
            while(substr($block,0,1) == "\0")
            {
                $block = substr($block,1);
            }
            $plain .= $block;
        }
        
        return $plain;
    }
    
    /** This method encrypt using CBC mode.
      *
      * In CBC mode each block is xored with the last. This function use $iv as
      * first block.
      *
      * @access private
      * @param  string $plain  the plain text to be decrypted
      * @return string $cipher the plain text encrypted
      */
    function _cbc_encrypt($plain)
    {
        $blocksize = $this->cipher->blocksize;
        $plainsize = strlen($plain);
        $cipher    = '';
        $lcipher   = $this->iv;
        
        // encrypt each block
        for($i = 0;$i < $plainsize;$i = $i + $blocksize)
        {
            $block = substr($plain,$i,$blocksize); 
            if(strlen($block) < $blocksize)
            {
                // pad block with '\0' 
                $block = str_pad($block,$blocksize,"\0",STR_PAD_LEFT);
            }
            // crypt the block xored with the last cipher block
            $lcipher = $this->cipher->_encrypt($block ^ $lcipher);
            $cipher .= $lcipher;
        }
        
        return $cipher;
    }
    
    /** This method decrypt using CBC.
      *
      * @access private
      * @param  string $cipher the cipher text
      * @return string $plain  the cipher text decrypted
      */
    function _cbc_decrypt($cipher)
    {
        // get the block size of the cipher
        $blocksize  = $this->cipher->blocksize;
        $ciphersize = strlen($cipher);
        $plain      = '';
        $lcipher    = $this->iv;
        
        for($i = 0;$i < $ciphersize;$i = $i + $blocksize)
        {
            $block   = substr($cipher,$i,$blocksize); 
            
            // xor the block with the last cipher block
            $dblock  = $lcipher ^ $this->cipher->_decrypt($block);
            $lcipher = $block;
            
            // Remove padded chars 
            while(substr($dblock,0,1) == "\0")
            {
                $dblock = substr($dblock,1);
            }
            $plain .= $dblock;
        }
        
        return $plain;
    }
    
    /**
      * A simple function for error handling.
      * 
      * TODO: Improve the error handling of the class 
      *
      * @access private
      * @param  string $message erro message
      * @return boolean true
      */
    function error($message)
    {
        //echo "Error: ".$message."<br>";   	

        return 1;
    }
}

?>
