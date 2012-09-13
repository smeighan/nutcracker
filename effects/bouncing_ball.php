<?php
/*
Nutcracker: RGB Effects Builder

    Copyright (C) 2012  Sean Meighan

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('../conf/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Nutcracker: RGB Effects Builder</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="googlebot" content="noarchive"/>
		<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
		<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
		<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php

//
require("read_file.php");




///*
/*
Array

(
    [username] => f
    [user_target] => MT
    [effect_class] => garlands
    [effect_name] => 44
    [number_garlands] => 4
    [number_rotations] => 2
    [garland_thickness] => 1
    [start_color] => #26FF35
    [end_color] => #2E35FF
    [frame_delay] => 22
    [direction] => 2
    [submit] => Submit Form to create your target model
)



 */ 





$array_to_save=$_GET;
$array_to_save['OBJECT_NAME']='bouncing_ball';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
$frame_delay = $_GET['frame_delay'];
$frame_delay = intval((5+$frame_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$frame_delay;

extract ($array_to_save);
save_user_effect($array_to_save);


//show_array($_GET,"_GET");
show_array($array_to_save,"Effect Settings");
//show_array($_SESSION,"_SESSION");
//show_array($_SERVER,"_SERVER");




list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;


$member_id=get_member_id($username);
$path ="../effects/workspaces/$member_id";
$directory=$path;
if (file_exists($directory)) {
} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}

$base = $user_target . "~" . $effect_name;

$t_dat = $user_target . ".dat";
$xdat = $user_target ."~".  $effect_name . ".dat";

$path="../targets/". $member_id;

purge_files();


$path="../effects/workspaces/". $member_id;

if(empty($show_frame)) $show_frame='N';
if(empty($background_color)) $background_color='#FFFFFF';

bouncing_ball($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$sparkles,$seq_duration,$show_frame,$radian_shift,$start_color,$end_color,$background_chunk,$background_skip,$background_color,$formula,$username);


$target_info=get_info_target($username,$t_dat);
show_array($target_info,'MODEL: ' . $t_dat);

show_elapsed_time($script_start,"Total Elapsed time for this effect:");


// function garland($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$sparkles,$seq_duration,$garland_gap,$garland,$show_frame)

function bouncing_ball($arr,$path,$t_dat,$base,$start_color,$end_color,$frame_delay,$window_degrees,$sparkles,$seq_duration,$show_frame,$radian_shift,$start_color,$end_color,$background_chunk,$background_skip,$background_color,$formula,$username)
{

	$BALLS = 20;                  # NUMBER OF SIMULATED BALLS

	$BALL_RADIUS = 25;            # RADIUS OF BALL IN PIXELS
	$START_SPACE = 50;            # SIDE OFFSET IN PIXELS
	$SCREEN_WIDTH = 500;          # WIDTH OF SCREEN IN PIXELS
	$SCREEN_HEIGHT = 350;         # HEIGHT OF SCREEN IN PIXELS
	$WALL_SPACE = 70;             # WIDTH OF WALLS IN PIXELS
	$FLOOR_SPACE = 15;            # HEIGHT OF FLOOR IN PIXELS

	$BACKGROUND = 'gray40';       # COLOR OF BACKGROUND
	$BALL_COLOR[] = ('red', 'orange', 'yellow');     # COLOR OF BALLS
	$FLOOR_COLOR = 'gray20';      # COLOR OF FLOOR
	$FORCE_COLOR = 'sienna4';     # COLOR OF FOURCE FIELD

	$FPS = 60;                    # FRAMES PER SECOND
	$SPEED_LIMIT = 2000;          # PIXELS PER SECOND
	$WALL_FORCE = 900;            # PIXELS PER SECOND
	$GRAV_RATE = 100;             # PIXELS PER SECOND
	$FRIC_RATE = 0.5;             # VELOCITY PER SECOND

	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	$strand_pixel=$arr[9];


	srand(time());
	$maxFrame=80;
	//$maxTrees=6;	// how many tree to draw at one time
	$seq_number=0;
	$window_array = getWindowArray($minStrand,$maxStrand,$window_degrees);

	# initialise balls
	for($b=0;$b<$BALLS;$b++)
	{
		$x = -$START_SPACE;
		if (rand(1, 100)> 50) $x=$START_SPACE + $SCREEN_WIDTH;
		$y = intval (rand($BALL_RADIUS, $SCREEN_HEIGHT - $FLOOR_SPACE - $BALL_RADIUS));
		$balls[$b]['x']=$x;
		$balls[$b]['y']=$y;
		$balls[$b]['radius']=$BALL_RADIUS;
	}
}
/*
	EXPLOSIVE EMBERS

## {{{ http://code.activestate.com/recipes/502255/ (r1)
import Tkinter
import random
import time
import traceback
import physics

################################################################################

BALLS = 20                  # NUMBER OF SIMULATED BALLS

BALL_RADIUS = 25            # RADIUS OF BALL IN PIXELS
START_SPACE = 50            # SIDE OFFSET IN PIXELS
SCREEN_WIDTH = 500          # WIDTH OF SCREEN IN PIXELS
SCREEN_HEIGHT = 350         # HEIGHT OF SCREEN IN PIXELS
WALL_SPACE = 70             # WIDTH OF WALLS IN PIXELS
FLOOR_SPACE = 15            # HEIGHT OF FLOOR IN PIXELS

BACKGROUND = 'gray40'       # COLOR OF BACKGROUND
BALL_COLOR = ('red',
	      'orange',
	      'yellow')     # COLOR OF BALLS
FLOOR_COLOR = 'gray20'      # COLOR OF FLOOR
FORCE_COLOR = 'sienna4'     # COLOR OF FOURCE FIELD

FPS = 60                    # FRAMES PER SECOND
SPEED_LIMIT = 2000          # PIXELS PER SECOND
WALL_FORCE = 900            # PIXELS PER SECOND
GRAV_RATE = 100             # PIXELS PER SECOND
FRIC_RATE = 0.5             # VELOCITY PER SECOND

################################################################################

def main():
    'Setup and start demonstration.'
    initialise()
    Tkinter.mainloop()

def initialise():
    'Build balls and prepare GUI.'
    global balls, x, y, screen, lock, start, frame
    balls = []
    for ball in xrange(BALLS):
	x = -START_SPACE if random.randint(0, 1) else START_SPACE + SCREEN_WIDTH
	y = random.randint(BALL_RADIUS, SCREEN_HEIGHT - FLOOR_SPACE - BALL_RADIUS)
	balls.append(physics.Ball(x, y, BALL_RADIUS))
    root = Tkinter.Tk()
    root.resizable(False, False)
    root.title('Explosive Embers')
    x = (root.winfo_screenwidth() - SCREEN_WIDTH) / 2
    y = (root.winfo_screenheight() - SCREEN_HEIGHT) / 2
    root.geometry('%dx%d+%d+%d' % (SCREEN_WIDTH, SCREEN_HEIGHT, x, y))
    root.bind_all('<Escape>', lambda event: event.widget.quit())
    root.bind('<Configure>', move)
    screen = Tkinter.Canvas(root, width=SCREEN_WIDTH, height=SCREEN_HEIGHT, background=BACKGROUND)
    screen.after(1000 / FPS, update)
    screen.after(10000 / FPS, unlock)
    screen.pack()
    floor_height = SCREEN_HEIGHT - FLOOR_SPACE + 2
    screen.create_rectangle(0, 0, WALL_SPACE - 1, floor_height, fill=FORCE_COLOR)
    screen.create_rectangle(SCREEN_WIDTH - WALL_SPACE + 1, 0, SCREEN_WIDTH, floor_height, fill=FORCE_COLOR)
    screen.create_line(0, floor_height, SCREEN_WIDTH, floor_height, width=3, fill=FLOOR_COLOR)
    lock = True
    start = time.clock()
    frame = 1.0

def move(event):
    'Simulate movement of screen.'
    global x, y
    if not lock:
	diff = physics.Vector(x - event.x, y - event.y)
	screen.move('animate', diff.x, diff.y)
	floor_height = SCREEN_HEIGHT - FLOOR_SPACE - BALL_RADIUS
	for ball in balls:
	    ball.pos += diff
	    if ball.pos.y >= floor_height:
		ball.vel.y += diff.y * FPS
		floor(ball)
	x, y = event.x, event.y

def update():
    'Run physics and update screen.'
    global frame
    try:
	for mutate in wall, floor, gravity, friction, governor:
	    for ball in balls:
		mutate(ball)
	for index, ball_1 in enumerate(balls[:-1]):
	    for ball_2 in balls[index+1:]:
		try: ball_1.crash(ball_2)
		except: pass
	for ball in balls:
	    ball.move(FPS)
	screen.delete('animate')
	for ball in balls:
	    x1 = ball.pos.x - ball.rad
	    y1 = ball.pos.y - ball.rad
	    x2 = ball.pos.x + ball.rad
	    y2 = ball.pos.y + ball.rad
	    screen.create_oval(x1, y1, x2, y2, fill=random.choice(BALL_COLOR), tag='animate')
	frame += 1
	screen.after(int((start + frame / FPS - time.clock()) * 1000), update)
    except:
	screen.delete(Tkinter.ALL)
	screen.create_text(SCREEN_WIDTH / 2, SCREEN_HEIGHT / 2, text=traceback.format_exc(), font='Courier 10', fill='red', tag='animate')

def wall(ball):
    'Simulate a wall.'
    space = WALL_SPACE + BALL_RADIUS
    force = float(WALL_FORCE) / FPS
    if ball.pos.x <= space:
	ball.vel.x += force
    elif ball.pos.x >= SCREEN_WIDTH - space:
	ball.vel.x -= force

def floor(ball):
    'Simulate a floor.'
    floor_height = SCREEN_HEIGHT - FLOOR_SPACE - BALL_RADIUS
    if ball.pos.y >= floor_height:
	ball.pos.y = floor_height
	ball.vel.y *= -1

def gravity(ball):
    'Simulate gravity.'
    ball.vel.y += float(GRAV_RATE) / FPS

def friction(ball):
    'Simulate friction.'
    ball.vel *= FRIC_RATE ** (1.0 / FPS)

def governor(ball):
    'Simulate speed governor.'
    if abs(ball.vel) > SPEED_LIMIT:
	ball.vel = ball.vel.unit() * SPEED_LIMIT

def unlock():
    'Activate the "move" function.'
    global lock
    lock = False

################################################################################

if __name__ == '__main__':
    main()
## end of http://code.activestate.com/recipes/502255/ }}}





import random           # FOR RANDOM BEGINNINGS
from Tkinter import *   # ALL VISUAL EQUIPMENT

WIDTH = 400             # OF SCREEN IN PIXELS
HEIGHT = 400            # OF SCREEN IN PIXELS
BALLS = 7               # IN SIMULATION
WALL = 50               # FROM SIDE IN PIXELS
WALL_FORCE = 400        # ACCELERATION PER MOVE
SPEED_LIMIT = 3000      # FOR ball VELOCITY
BALL_RADIUS = 5         # FOR ballS IN PIXELS
OFFSET_START = 20       # FROM WALL IN PIXELS
FRAMES_PER_SEC = 40     # SCREEN UPDATE RATE

################################################################################

def main():
    # Start the program.
    initialise()
    mainloop()

def initialise():
    # Setup simulation variables.
    global active
    active = False
    build_balls()
    build_graph()

def build_graph():
    # Build GUI environment.
    global graph, left, top
    root = Tk()
    root.resizable(False, False)
    root.title('Balls')
    left = (root.winfo_screenwidth() - WIDTH) / 2
    top = (root.winfo_screenheight() - HEIGHT) / 2
    root.geometry('%dx%d+%d+%d' % (WIDTH, HEIGHT, left, top))
    root.bind_all('<Escape>', lambda event: event.widget.quit())
    root.bind('<Configure>', window_move)
    graph = Canvas(root, width=WIDTH, height=HEIGHT, background='white')
    graph.after(1000 / FRAMES_PER_SEC, update)
    graph.after(1000, activate)
    graph.pack()

def activate():
    # Active window_move event.
    global active
    active = True

def window_move(event):
    # Respond to movements.
    global left, top
    if active:
	diff = TwoD(left - event.x, top - event.y)
	for ball in balls:
	    if HEIGHT - WALL - 2 < ball.position.y and top > event.y:
		ball.velocity.y -= (1000 * (top - event.y))
	    ball.position += diff
	left, top = event.x, event.y

def update():
    # Main simulation loop.
    graph.after(1000 / FRAMES_PER_SEC, update)
    draw()
    move()

def draw():
    graph.delete(ALL)
    # Draw sides.
    graph.create_rectangle((0, 0, WALL - BALL_RADIUS, HEIGHT), fill='light green')
    graph.create_rectangle((WIDTH - WALL + BALL_RADIUS, 0, WIDTH, HEIGHT), fill='light green')
    # Draw floor.
    y = HEIGHT - WALL + BALL_RADIUS + 2
    graph.create_line((WALL - BALL_RADIUS, y, WIDTH - WALL + BALL_RADIUS, y), fill='blue', width=3)
    # Draw all balls.
    for ball in balls:
	x1 = ball.position.x - BALL_RADIUS
	y1 = ball.position.y - BALL_RADIUS
	x2 = ball.position.x + BALL_RADIUS
	y2 = ball.position.y + BALL_RADIUS
	graph.create_oval((x1, y1, x2, y2), fill='red')
    graph.update()

def move():
    # Move all balls.
    for force in simulate_wall, simulate_gravity, simulate_friction:
	for ball in balls:
	    force(ball)
    for ball in balls:
	ball.update_velocity(balls)
    for ball in balls:
	ball.move()

def simulate_wall(ball):
    # Create viewing boundaries.
    if ball.position.x < WALL:
	ball.velocity.x += WALL_FORCE
    elif ball.position.x > WIDTH - WALL:
	ball.velocity.x -= WALL_FORCE

    if ball.position.y >= HEIGHT - WALL:
	ball.velocity.y *= -1
	ball.position.y = HEIGHT - WALL

def simulate_gravity(ball):
    # Create a pull.
    ball.velocity.y += 50

def simulate_friction(ball):
    # Slow velocity down.
    ball.velocity *= .9925

def limit_speed(ball):
    # Limit ball speed.
    if ball.velocity.mag() > SPEED_LIMIT:
	ball.velocity /= ball.velocity.mag() / SPEED_LIMIT

def build_balls():
    # Create balls variable.
    global balls
    balls = tuple(Ball(WIDTH, HEIGHT, OFFSET_START, FRAMES_PER_SEC) for ball in xrange(BALLS))

################################################################################

# TWO DIMENTIONAL VECTOR CLASS

class TwoD:

    def __init__(self, x, y):
	self.x = float(x)
	self.y = float(y)

    def __repr__(self):
	return 'TwoD(%s, %s)' % (self.x, self.y)

    def __add__(self, other):
	return TwoD(self.x + other.x, self.y + other.y)

    def __sub__(self, other):
	return TwoD(self.x - other.x, self.y - other.y)

    def __mul__(self, other):
	return TwoD(self.x * other, self.y * other)

    def __div__(self, other):
	return TwoD(self.x / other if other else self.x, self.y / other if other else self.y)

    def __iadd__(self, other):
	self.x += other.x
	self.y += other.y
	return self

    def __isub__(self, other):
	self.x -= other.x
	self.y -= other.y
	return self

    def __imul__(self, other):
	self.x *= other
	self.y *= other
	return self

    def __idiv__(self, other):
	self.x /= other
	self.y /= other
	return self

    def mag(self):
	return ((self.x ** 2) + (self.y ** 2)) ** 0.5

################################################################################

# BALL IMPLEMENTATION CLASS

class Ball:

    def __init__(self, width, height, offset, move_divider):
	self.velocity = TwoD(0, 0)
	self.position = TwoD(*(-offset if random.randint(0, 1) else width + offset, random.randint(1, height)))
	self.move_divider = move_divider * 5

    def update_velocity(self, balls):
	vector = TwoD(0, 0)
	for ball in balls:
	    if ball is not self:
		if (self.position - ball.position).mag() < (BALL_RADIUS * 2.5):
		    vector -= (ball.position - self.position)
	self.__temp = vector * self.velocity.mag() / vector.mag()

    def move(self):
	self.velocity += self.__temp
	limit_speed(self)
	self.position += self.velocity / self.move_divider

################################################################################

# Execute the simulation.
if __name__ == '__main__':
    main()
 */

?>

