export const resize_window = (self) => {
	window.addEventListener('resize', function() {
	    self.mobile = self.isMobile()
	})
}