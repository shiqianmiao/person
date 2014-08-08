<?php if ($this->resultMsg['success']) {?>
    <div class="msg m4">
        <i class="i2"></i>
        <?php echo $this->resultMsg['success']; ?>
    </div>
<?php } else if ($this->resultMsg['error']) {?>
    <div class="msg m2">
        <i class="i3"></i>
        <?php echo $this->resultMsg['error']; ?>
    </div>
<?php }?>