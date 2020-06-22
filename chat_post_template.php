<?php get_header();
$chat_id = get_the_ID();
$appearance = (array)get_post_meta($chat_id, 'appearance', true);
?>
<section class="chat-page-container">
	<div class="fullwidth-preview" style="width: 100%;min-height: 500px;padding: 20px;background:<?= $appearance['bgcolor']?>;color: <?= $appearance['color'];?>">
			<div class="row" >
				<?php if($appearance['featured_image']){?>
				<div class="col-md-6">
					<img src="<?= $appearance['featured_image'];?>" width="100%">
				</div>
				<?php }?>
				<div class="<?= $appearance['featured_image'] ? 'col-md-6' : 'col-md-12'?>">
					<h2 style="color:<?= $appearance['color'];?>" ><?= $appearance['heading'];?></h2>
					<h5 style="color:<?= $appearance['color'];?>" ><?= $appearance['subheading'];?></h5>
					<div>
						<?= $appearance['subheading'];?>
					</div>
					<?php if($appearance['img_after_content']){?>
						<img width="100%" src="<?= $appearance['img_after_content']?>">
					<?php }?>
					<?php if($appearance['button_text']){?>
						<button class="btn btn-primary">
							<i class="fa <?= $appearance['button_icon2']?>"></i>
							<?= $appearance['button_text']?>
						</button>
					<?php }?>
				</div>
			</div>
		</div>
</section>
<?php get_footer();