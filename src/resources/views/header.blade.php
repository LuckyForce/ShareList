<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $title; ?>
    </title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <script>
        let activeClassName = 'underline'

        //Constant to see if the navbar is extended, in case the screen is too small
        let expanded = false

        //Function to expand contract Navbar
        const expandHeader = () => {
            expanded = !expanded
            if (expanded) {
                document.getElementById('expandDiv')?.classList.add('block')
                document.getElementById('expandDiv')?.classList.remove('hidden')
            } else {
                document.getElementById('expandDiv')?.classList.add('hidden')
                document.getElementById('expandDiv')?.classList.remove('block')
            }
            console.log(expanded)
        }
    </script>
<?php
    $activeClassName = 'underline';
?>
<nav class='bg-1 text-1 py-4 flex-row px-4 border-b-2'>
    <ul class='lg:flex lg:flex-row lg:space-x-5 grid grid-cols-2'>
        <li class='ml-2 my-auto'>
            <a href='<?php echo url('/'); ?>'>
                <span class='<?php if($url=="home") echo $activeClassName ?> navlink-1 flex flex-row items-center md:text-3xl sm:text-2xl text-xl'>
                    <img className='h-10 w-10 mx-2' src='https://cdn.discordapp.com/attachments/669591310004649987/930035678782292029/bingomaker_v8.png'></img>
                </span>
            </a>
        </li>
        <!--
        <li className='lg:flex hidden text-4xl navbar-div-color-1'>|</li>
        <li className='lg:hidden text-right mr-2 my-auto'>
            <button onClick={expandHeader}>Expand/Contract</button>
        </li>
        <div id='expandDiv' className='lg:flex lg:flex-row lg:space-x-5 hidden lg:mt-0 mt-5 justify-between w-full'>
            <li className='lg:ml-0 ml-2 my-auto flex flex-row'>
                <form action='/browse/' className='flex flex-row bg-2 shadow-md shadow-black/50 rounded-lg'>
                    <button type='submit' className='w-10 h-10'>
                        <SearchIcon className='icon-2 h-5 px-2'></SearchIcon>
                    </button>
                    <TextInput type='search' placeholder='Search...' className='h-10 bg-2 rounded-lg' name='q'></TextInput>
                </form>
            </li>
            <div className='lg:flex lg:flex-row lg:space-x-6'>
                <li className='lg:ml-0 ml-2 lg:my-auto mt-5'>
                    <NavLink to='lobbies'>
                        {({ isActive }) => (
                        <span className={ (isActive ? activeClassName : '' ) + ' navlink-1 flex flex-row items-center bg-2 py-1 px-4 shadow-md shadow-black/50 rounded-lg' }>
                            <ViewGridIcon className='icon-1 h-5 mr-2'></ViewGridIcon> Lobbies
                        </span>
                        )}
                    </NavLink>
                </li>
                <li className='lg:ml-0 ml-2 lg:my-auto mt-3'>
                    <NavLink to='templates'>
                        {({ isActive }) => (
                        <span className={ (isActive ? activeClassName : '' ) + ' navlink-1 flex flex-row items-center bg-2 py-1 px-4 shadow-md shadow-black/50 rounded-lg' }>
                            <TemplateIcon className='icon-1 h-5 mr-2'></TemplateIcon> Templates
                        </span>
                        )}
                    </NavLink>
                </li>
                <li className='lg:ml-0 ml-2 lg:my-auto mt-3'>
                    <NavLink to='about'>
                        {({ isActive }) => (
                        <span className={ (isActive ? activeClassName : '' ) + ' navlink-1 flex flex-row items-center py-1 px-4 bg-2 shadow-md shadow-black/50 rounded-lg' }>
                            <InformationCircleIcon className='icon-1 h-5 mr-2'></InformationCircleIcon>{' '}
                            About
                        </span>
                        )}
                    </NavLink>
                </li>
                <div className='flex flex-row space-x-5 lg:mt-0 mt-3'>
                    <div className='dropdown my-auto'>
                        <li className='lg:ml-0 ml-2 my-auto drop-button'>
                            <PlusIcon className='icon-1 h-7'></PlusIcon>
                        </li>
                        <div className='dropdown-content hidden'>
                            <NavLink to='host'>
                                {({ isActive }) => (
                                <span className={ (isActive ? activeClassName : '' ) + ' navlink-1 flex flex-row items-center bg-1' }>
                                    Host Lobby
                                </span>
                                )}
                            </NavLink>
                            <NavLink to='template/create'>
                                {({ isActive }) => (
                                <span className={ (isActive ? activeClassName : '' ) + ' navlink-1 flex flex-row items-center bg-1' }>
                                    Create Template
                                </span>
                                )}
                            </NavLink>
                        </div>
                    </div>
                    <li className='my-auto'>
                        <BellIcon className='icon-1 h-7'></BellIcon>
                    </li>
                    <li className='lg:flex hidden text-4xl  navbar-div-color-1'>|</li>
                    <li className='my-auto'>
                        <NavLink to='profile'>
                            {({ isActive }) => (
                            <span className={ (isActive ? activeClassName : '' ) + ' navlink-1 flex flex-row items-center bg-1' }>
                                <UserCircleIcon className='icon-1 h-10 mr-1'></UserCircleIcon>
                            </span>
                            )}
                        </NavLink>
                    </li>
                </div>
            </div>
        </div>
        -->
    </ul>
</nav>
<div class="content">