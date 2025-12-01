<?php    
    //Matriz de películas por defecto
    //La matriz original era la siguiente:
    /*
    $peliculas = [
        ["titulo" => "El editor de libros", "año" => 2016, "director" => "Michael Grandage", "actores" => "Colin Firth, Jude Law, Nicole Kidman", "genero" => "Biografía"],
        ["titulo" => "Un amor entre dos mundos", "año" => 2012, "director" => "Juan Diego Solanas", "actores" => "Jim Sturgess, Kirsten Dunst, Timothy Spall", "genero" => "Ciencia ficción"],
        ["titulo" => "Una cuestión de tiempo", "año" => 2013, "director" => "Richard Curtis", "actores" => "Domhnall Gleeson, Rachel McAdams, Bill Nighy", "genero" => "Romance"],
        ["titulo" => "El indomable Will Hunting", "año" => 1997, "director" => "Gus Van Sant", "actores" => "Matt Damon, Robin Williams, Ben Affleck", "genero" => "Drama"],
        ["titulo" => "Descubriendo a Forrester", "año" => 2000, "director" => "Gus Van Sant", "actores" => "Sean Connery, Rob Brown, F. Murray Abraham, Anna Paquin", "genero" => "Drama"],
        ["titulo" => "El club de los poetas muertos", "año" => 1989, "director" => "Peter Weir", "actores" => "Robin Williams, Robert Sean Leonard, Ethan Hawke, Josh Charles", "genero" => "Drama"],
        ["titulo" => "Gattaca", "año" => 1997, "director" => "Andrew Niccol", "actores" => "Ethan Hawke, Uma Thurman, Jude Law, Loren Dean", "genero" => "Ciencia ficción"],
        ["titulo" => "In Time", "año" => 2011, "director" => "Andrew Niccol", "actores" => "Justin Timberlake, Amanda Seyfried, Vincent Kartheiser", "genero" => "Ciencia ficción"],
        ["titulo" => "Una mente maravillosa", "año" => 2001, "director" => "Ron Howard", "actores" => "Russell Crowe, Ed Harris, Jennifer Connelly", "genero" => "Biografía"],
        ["titulo" => "Big Fish", "año" => 2003, "director" => "Tim Burton", "actores" => "Ewan McGregor, Albert Finney, Billy Crudup, Jessica Lange", "genero" => "Drama"],
        ["titulo" => "El club de la lucha", "año" => 1999, "director" => "David Fincher", "actores" => "Edward Norton, Brad Pitt, Helena Bonham Carter", "genero" => "Thriller"],
        ["titulo" => "Eduardo Manostijeras", "año" => 1990, "director" => "Tim Burton", "actores" => "Johnny Depp, Winona Ryder, Dianne Wiest", "genero" => "Fantasía"]
    ];
    */
    //Pero la cambiamos por objetos de tipo película
    require_once "models/pelicula.php";

    $peliculas = [
        new Pelicula("El editor de libros", 2016, "Michael Grandage", "Colin Firth, Jude Law, Nicole Kidman", "Biografía"),
        new Pelicula("Un amor entre dos mundos", 2012, "Juan Diego Solanas", "Jim Sturgess, Kirsten Dunst, Timothy Spall", "Ciencia ficción"),
        new Pelicula("Una cuestión de tiempo", 2013, "Richard Curtis", "Domhnall Gleeson, Rachel McAdams, Bill Nighy", "Romance"),
        new Pelicula("El indomable Will Hunting", 1997, "Gus Van Sant", "Matt Damon, Robin Williams, Ben Affleck", "Drama"),
        new Pelicula("Descubriendo a Forrester", 2000, "Gus Van Sant", "Sean Connery, Rob Brown, F. Murray Abraham, Anna Paquin", "Drama"),
        new Pelicula("El club de los poetas muertos", 1989, "Peter Weir", "Robin Williams, Robert Sean Leonard, Ethan Hawke, Josh Charles", "Drama"),
        new Pelicula("Gattaca", 1997, "Andrew Niccol", "Ethan Hawke, Uma Thurman, Jude Law, Loren Dean", "Ciencia ficción"),
        new Pelicula("In Time", 2011, "Andrew Niccol", "Justin Timberlake, Amanda Seyfried, Vincent Kartheiser", "Ciencia ficción"),
        new Pelicula("Una mente maravillosa", 2001, "Ron Howard", "Russell Crowe, Ed Harris, Jennifer Connelly", "Biografía"),
        new Pelicula("Big Fish", 2003, "Tim Burton", "Ewan McGregor, Albert Finney, Billy Crudup, Jessica Lange", "Drama"),
        new Pelicula("El club de la lucha", 1999, "David Fincher", "Edward Norton, Brad Pitt, Helena Bonham Carter", "Thriller"),
        new Pelicula("Eduardo Manostijeras", 1990, "Tim Burton", "Johnny Depp, Winona Ryder, Dianne Wiest", "Fantasía"),
        new Serie("Hijos de la Anarquía", 2008, "Kurt Sutter", "Charlie Hunnam, Ron Perlman, Katey Sagal", "Drama", 8),
        new Serie("Peaky Blinders", 2013, "Steven Knight", "Cillian Murphy, Paul Anderson, Helen McCrory", "Drama", 5),
        new Corto("Frankenweenie", 1984, "Tim Burton", "Shelley Duvall, Daniel Stern, Barrett Oliver", "Ciencia Ficción", 20)
    ];
?>