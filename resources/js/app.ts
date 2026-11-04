import { start } from './engine/swup';
import { setupMotion } from './motion/setup';
import { initContactForm } from './contact';

start(setupMotion);
initContactForm();
