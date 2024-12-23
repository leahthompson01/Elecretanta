import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import SecondaryButton from "@/Components/SecondaryButton";
import gear from "../../assets/icons/gear.svg";
import gift from "../../assets/icons/gift.svg";
import heart from "../../assets/icons/heart.svg";
import lightbulb from "../../assets/icons/lightbulb.svg";
import PrimaryButton from "@/Components/PrimaryButton";

export default function Dashboard() {
    const dashboardItems = [
        {
            image: gift,
            title: "gift exchanges",
            link: "/dashboard",
        },
        {
            image: heart,
            title: "hobbies",
            link: "/dashboard",
        },
        {
            image: lightbulb,
            title: "ideas",
            link: "/dashboard",
        },
        {
            image: gear,
            title: "settings",
            link: "/dashboard",
        },
    ];
    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            {/* <SecondaryButton className="ms-4">Test</SecondaryButton>
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            You're logged in!
                        </div>
                    </div>
                </div>
            </div> */}
            <h1 className="sr-only">Dashboard</h1>
            <h2 className="leading-tight text-black font-bold text-5xl tracking-[-.005em] font-baskerville">
                Hello, RICHARD
            </h2>
            <ul className="grid grid-cols-[repeat(auto-fit,minmax(15.625rem,1fr))] gap-6 justify-between pt-12 pb-16">
                {dashboardItems.map((item) => {
                    const { image, title, link } = item;
                    return (
                        <li className="border border-solid border-[#B88914] rounded-2xl flex-1 bg-[#FAF7EE] px-9 py-8 flex flex-col justify-center relative transition duration-200 [&:has(a:hover)]:shadow-xl [&:has(a:hover)]:-translate-y-1 md:min-h-[20rem]">
                            <div className="pb-4">
                                <img src={image} alt="" className="mx-auto" />
                            </div>
                            <h2 className="capitalize font-bold text-2xl text-center ">
                                <a
                                    href={link}
                                    className="after:absolute after:left-0 after:top-0 after:w-full after:h-full"
                                >
                                    {title}
                                </a>
                            </h2>
                        </li>
                    );
                })}
            </ul>
            <PrimaryButton
                href="/"
                className="w-full font-baskerville justify-center py-4 text-lg md:text-2xl md:max-w-max px-10 font-bold tracking-[-0.01em] self-center capitalize"
            >
                Make your list
            </PrimaryButton>
        </AuthenticatedLayout>
    );
}
