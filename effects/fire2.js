import java.awt.*;
import java.awt.event.*;
import java.util.Random;
import javax.swing.Timer;


public class Fire2 extends java.applet.Applet 
	implements ActionListener
{
	Image img;			// Image for drawing fire off-screen
	Graphics g2;		// Graphics object for painting on img
	Graphics g1;		// Graphics object for painting on canvas
	Canvas canvas;		// Canvas (an on-screen image)
	Button b1;			// For adjusting fire intensity
	Button b2;			// For adjusting fire intensity
	Button b3;			// For adjusting fire intensity
	Button b4;			// For adjusting fire intensity
	Button b5;			// For adjusting fire intensity
	Button b6;			// For adjusting fire intensity
	Button b7;			// For adjusting fire intensity
	Button b8;			// For adjusting fire intensity
	Button red;			// For red flames
	Button blue;		// For blue flames
	Button odd;			// For odd flames
	Random rng;			// Random number generator
	Timer timer;		// A timer to tick every 3 ms
	Color colors[];		// Color gradient array
	int values[][][];		// Two 2D arrays of fire color indices
	int v;			// First index for values array (alternates between 0 and 1)
	int color1,color2;

	// Initialize applet
	public void init()
	{
		v = 0;

		// RANDOM NUMBER GENERATOR
		rng = new Random();

		// CANVAS TO DISPLAY FIRE
		canvas = new Canvas();
		canvas.setSize(500, 200);	// Choose image size here
		add(canvas);

		// FLAME INTENSITY BUTTONS
		b1 = new Button("1");
		b2 = new Button("2");
		b3 = new Button("3");
		b4 = new Button("4");
		b5 = new Button("5");
		b6 = new Button("6");
		b7 = new Button("7");
		b8 = new Button("8");
		add(b1);
		add(b2);
		add(b3);
		add(b4);	color1 = 0; color2 = 99; // The default
		add(b5);
		add(b6);
		add(b7);
		add(b8);
		b1.addActionListener(this);
		b2.addActionListener(this);
		b3.addActionListener(this);
		b4.addActionListener(this);
		b5.addActionListener(this);
		b6.addActionListener(this);
		b7.addActionListener(this);
		b8.addActionListener(this);

		// FLAME COLOR BUTTONS
		red = new Button("red");
		blue = new Button("blue");
		odd = new Button("odd");
		add(red);
		add(blue);
		add(odd);
		red.addActionListener(this);
		blue.addActionListener(this);
		odd.addActionListener(this);

		// GRAPHICS, INCLUDING OFF-SCREEN IMAGE
		g1 = canvas.getGraphics();
		img = createImage(canvas.getWidth(),canvas.getHeight());
		g2 = img.getGraphics();

		// COLOR GRADIENT
		init_colors_red();

		// 2D FIRE ARRAY
		init_values();

		// ANIMATION TIMER
		timer = new Timer(3, this);
		timer.start();
	}

	// Initialize color array with gradient from color c1 to c2 to c3 to c4
	public void init_colors(Color c1,Color c2,Color c3,Color c4)
	{
		colors = new Color [100];
		for (int i = 0; i < 33; i++)
		{
			double f = ((double)i) / 32;
			colors[i] = new Color(
				clamp((int)( c1.getRed() + (f * (c2.getRed() - c1.getRed())) )),
				clamp((int)( c1.getGreen() + (f * (c2.getGreen() - c1.getGreen())) )),
				clamp((int)( c1.getBlue() + (f * (c2.getBlue() - c1.getBlue())) ))
			);
		}
		for (int i = 33; i < 66; i++)
		{
			double f = ((double)(i - 33)) / 32;
			colors[i] = new Color(
				clamp((int)( c2.getRed() + (f * (c3.getRed() - c2.getRed())) )),
				clamp((int)( c2.getGreen() + (f * (c3.getGreen() - c2.getGreen())) )),
				clamp((int)( c2.getBlue() + (f * (c3.getBlue() - c2.getBlue())) ))
			);
		}
		for (int i = 66; i < 100; i++)
		{
			double f = ((double)(i - 66)) / 32;
			colors[i] = new Color(
				clamp((int)( c3.getRed() + (f * (c4.getRed() - c3.getRed())) )),
				clamp((int)( c3.getGreen() + (f * (c4.getGreen() - c3.getGreen())) )),
				clamp((int)( c3.getBlue() + (f * (c4.getBlue() - c3.getBlue())) ))
			);
		}
	}

	// Initialize array for color gradient: white - orange - red - black
	public void init_colors_red()
	{	
		Color c1 = new Color(0,0,0);		// BLACK		
		Color c2 = new Color(255,0,0);	// RED
		Color c3 = new Color(255,170,0);	// ORANGE
		Color c4 = new Color(255,255,255);	// WHITE
		
		init_colors(c1,c2,c3,c4);
	}

	// Initialize array for color gradient: white - green - blue - black
	public void init_colors_blue()
	{	
		Color c1 = new Color(0,0,0);		// BLACK
		Color c2 = new Color(0,0,255);	// BLUE
		Color c3 = new Color(100,255,100);	// GREEN
		Color c4 = new Color(255,255,255);	// WHITE
		
		init_colors(c1,c2,c3,c4);
	}

	// Initialize array for color gradient: black - white - black - black
	public void init_colors_odd()
	{	
		Color c1 = new Color(0,0,0);		// BLACK
		Color c2 = new Color(255,255,255);	// WHITE
		Color c3 = new Color(0,0,0);		// BLACK
		Color c4 = new Color(0,0,0);		// BLACK
		
		init_colors(c1,c2,c3,c4);
	}

	// Clamp input to range [0,..,255]
	public int clamp(int i)
	{
		if (i < 0) return(0);
		if (i > 255) return(255);
		return(i);
	}

	// Initialize 2D array of color indices for fire calculations
	public void init_values()
	{
		int rows = (canvas.getHeight() / 2);
		int cols = (canvas.getWidth() / 2);
		values = new int [2][rows][cols];
		for (int i = 0; i < 2; i++)
		{
			for (int r = 0; r < rows; r++)
			{
				for (int c = 0; c < cols; c++)
				{
					values[i][r][c] = 0;
				}
			}
		}	
	}

	// Pain the final off-screen image onto the screen in one quick step
	public void paintImage()
	{
		g1.drawImage(img,0,0,this);
	}

	// What to do each time the timer ticks (or the user clicks a button)
	public void actionPerformed(ActionEvent event)
	{
		Object source = event.getSource();

		if (source == timer)
		{
			speckle();		// Modify the bottom row for the fire
			disperse();		// Apply the simple fire algorithm everywhere else
			paintImage();	// Display the new image
			return;
		}

		if (source == b1)		{ color1 = 0; color2 = 9; }
		else if (source == b2)	{ color1 = 0; color2 = 39; }
		else if (source == b3)	{ color1 = 0; color2 = 69; }
		else if (source == b4)	{ color1 = 0; color2 = 99; }
		else if (source == b5)	{ color1 = 25; color2 = 99; }
		else if (source == b6)	{ color1 = 40; color2 = 99; }
		else if (source == b7)	{ color1 = 55; color2 = 99; }
		else if (source == b8)	{ color1 = 70; color2 = 99; }
		else if (source == red)		init_colors_red();
		else if (source == blue)	init_colors_blue();
		else if (source == odd)		init_colors_odd();
	}

	// Modify the bottom row of pixels for the fire
	public void speckle()
	{
		int r = (canvas.getHeight() / 2) - 1;
		int cols = (canvas.getWidth() / 2);
		for (int i = 0; i < cols; i++)
		{
			int x = rng.nextInt(25);
			if (x == 0)
				// 1/25 chance of changing bottom row pixel to black	       
				{ 
					values[0][r][i] = color1; 
					values[1][r][i] = color1;
				}
			else if (x == 1)  
				// 1/25 chance of changing bottom row pixel to white
				{ 
					values[0][r][i] = color2; 
					values[1][r][i] = color2;
				}
			// 23/25 chance of leaving bottom pixel alone
		}
	}

	// Apply fire algorithm everywhere except..
	// bottom row
	// and side columns
	public void disperse()
	{
		int rows = (canvas.getHeight() / 2) - 1;
		int cols = (canvas.getWidth() / 2) - 1;
		for (int r = 0; r < rows; r++)
		{
			for (int c = 1; c < cols; c++)
			{
				int sum = 
					values[1-v][r+1][c-1] + 
					values[1-v][r+1][c] + 
					values[1-v][r+1][c+1] +
					values[1-v][r][c-1] +
					values[1-v][r][c+1];
				values[v][r][c] = (sum / 5) - 1;
				if (values[v][r][c] < 0) values[v][r][c] = 0;
				g2.setColor(colors[values[v][r][c]]);
				g2.fillRect(c*2,r*2,2,2);
			}
		}
		v = 1 - v;
	}

}