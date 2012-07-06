#	 workspaces/2/AA+CANADAC.gp
set notitle
set ylabel
set xlabel
set xrange[  -40:   40]
set yrange[  -40:   40]
set zrange[  -39:  144]
unset border
set angles degrees
set object 1 rectangle from screen 0,0 to screen 1,1 fillcolor rgb "black" behind
unset key
set view 105,270, 2.0, 1
set style data lines
set noxtics
set noytics
set noztics
set style line 1  linetype 1 linecolor rgb "red"  linewidth 3.000 pointtype 1 pointsize default
set style line 2  linetype 2 linecolor rgb "green"  linewidth 3.000 pointtype 2 pointsize default
set style line 3  linetype 3 linecolor rgb "blue"  linewidth 3.000 pointtype 3 pointsize default
set style line 4  linetype 4 linecolor rgb "yellow"  linewidth 3.000 pointtype 4 pointsize default
set style line 5  linetype 5 linecolor rgb "cyan"  linewidth 3.000 pointtype 5 pointsize default
set style line 6  linetype 6 linecolor rgb "orange"  linewidth 3.000 pointtype 6 pointsize default
set style line 7  linetype 7 linecolor rgb "gray"  linewidth 1.000 pointtype 7 pointsize 0.3


set terminal gif animate notransparent noenhanced  delay 11 size 363,656
set output 'workspaces/2/AA+CANADAC.gif'
   splot 'workspaces/2/AA+CANADAC_d_1.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_2.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_3.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_4.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_5.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_6.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_7.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_8.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_9.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+CANADAC_d_10.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle

set output


set terminal gif animate notransparent noenhanced  delay 11 size 100,200
set output 'workspaces/2/AA+CANADAC_th.gif'
   splot 'workspaces/2/AA+CANADAC_d_1.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_2.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_3.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_4.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_5.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_6.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_7.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_8.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_9.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+CANADAC_d_10.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
